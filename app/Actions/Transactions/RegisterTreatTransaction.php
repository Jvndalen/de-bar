<?php

namespace App\Actions\Transactions;

use App\Events\TransactionCreated;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TreatBalance;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterTreatTransaction
{
    use AsAction;

    /**
     * @throws \Throwable
     */
    public function handle(
        string $userId,
        string $productId,
        int    $priceInCents,
        string $treatBalanceId
    ): string
    {
        return DB::transaction(function () use ($userId, $productId, $priceInCents, $treatBalanceId) {
            // Lock all relevant rows
            $user = User::lockForUpdate()->findOrFail($userId);
            $product = Product::lockForUpdate()->findOrFail($productId);
            $treatBalance = TreatBalance::lockForUpdate()->findOrFail($treatBalanceId);

            // Validate ownership
            if ($treatBalance->user_id !== $userId) {
                throw new \Exception("Treat balance does not belong to this user");
            }

            // Validate stock
            if ($product->quantity < 1) {
                throw new InsufficientStockException("Product {$product->name} is out of stock");
            }

            // Validate treat balance
            if ($treatBalance->remaining_amount < $priceInCents) {
                throw new InsufficientBalanceException("Insufficient treat balance");
            }

            if (!$treatBalance->is_active) {
                throw new \Exception("Treat balance is not active");
            }

            // Perform updates
            $product->decrement('quantity');
            $treatBalance->decrement('remaining_amount', $priceInCents);

            // Deactivate if depleted
            if ($treatBalance->remaining_amount <= 0) {
                $treatBalance->update(['is_active' => false]);
            }

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'total' => $priceInCents,
                'treat_balance_id' => $treatBalanceId,
                'type' => 'treat',
            ]);

            DB::afterCommit(function () use ($userId) {
                Cache::increment("analytics:transaction:amount:{$userId}");
            });

            event(new TransactionCreated($transaction));

            return $transaction->id;
        });
    }
}
