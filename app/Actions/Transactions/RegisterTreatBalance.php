<?php

namespace App\Actions\Transactions;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TreatBalance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterTreatBalance
{
    use AsAction;

    public function handle(string $userId, int $cents): TreatBalance
    {
        return DB::transaction(function () use ($userId, $cents) {
            $user = User::lockForUpdate()->findOrFail($userId);

            // Check for existing active treat balance
            $existingBalance = TreatBalance::where('user_id', $userId)
                ->where('is_active', true)
                ->where('remaining_amount', '>', 0)
                ->exists();

            if ($existingBalance) {
                throw new \Exception("Er is al een actief trakteer saldo");
            }

            // Validate sufficient balance
            if ($user->rawBalance() < $cents) {
                throw new InsufficientBalanceException("Niet genoeg saldo");
            }

            // Deduct from main balance
            $user->creditBalance($cents);

            // Create treat balance
            return TreatBalance::create([
                'user_id' => $userId,
                'initial_amount' => $cents,
                'remaining_amount' => $cents,
                'is_active' => true,
            ]);
        });
    }
}
