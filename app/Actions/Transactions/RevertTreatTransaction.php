<?php

namespace App\Actions\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RevertTreatTransaction
{
    use AsAction;

    public function handle(string $transactionId): void
    {
        DB::transaction(function () use ($transactionId) {
            $transaction = Transaction::with(['user', 'product', 'treatBalance'])
                ->lockForUpdate()
                ->findOrFail($transactionId);

            if ($transaction->reverted_at !== null) {
                throw new \Exception("Transaction already reverted");
            }

            if (!$transaction->treat_balance_id) {
                throw new \Exception("Not a treat transaction");
            }

            $user = $transaction->user()->lockForUpdate()->first();
            $product = $transaction->product()->lockForUpdate()->first();
            $treatBalance = $transaction->treatBalance()->lockForUpdate()->first();

            // Restore treat balance
            $treatBalance->increment('remaining_amount', $transaction->total);

            // Reactivate if needed
            if (!$treatBalance->is_active && $treatBalance->remaining_amount > 0) {
                $treatBalance->update(['is_active' => true]);
            }

            // Restore product quantity
            $product->increment('quantity');

            // Mark as reverted
            $transaction->update(['reverted_at' => now()]);

            DB::afterCommit(function () use ($user) {
                Cache::decrement("analytics:transaction:amount:{$user->id}");
            });
        });
    }
}
