<?php

namespace App\Actions\Transactions;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TreatBalance;
use App\Events\TransactionCreated;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InsufficientStockException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterTransaction
{
    use AsAction;

    /**
     * @throws \Throwable
     */
    public function handle(string $userId, string $productId, int $amountInCents): string
    {
        return DB::transaction(function () use ($userId, $productId, $amountInCents) {
// Lock rows to prevent race conditions
            $user = User::lockForUpdate()->findOrFail($userId);
            $product = Product::lockForUpdate()->findOrFail($productId);

// Validate stock
            if ($product->quantity < 1) {
                throw new InsufficientStockException("Product {$product->name} is out of stock");
            }

// Validate balance
//            if ($user->rawBalance() < $amountInCents) {
//                throw new InsufficientBalanceException("Insufficient balance for user {$user->name}");
//            }

// Update product quantity
            $product->decrement('quantity');

// Update user balance
//            $user->creditBalance($amountInCents);
// Create transaction
            $transaction = Transaction::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'total' => $amountInCents,
                'type' => 'standard',
            ]);

// Increment analytics (outside main transaction for performance)
            DB::afterCommit(function () use ($userId) {
                Cache::increment("analytics:transaction:amount:{$userId}");
            });

// Dispatch event for additional side effects
            event(new TransactionCreated($transaction));

            return $transaction->id;
        });
    }
}
