<?php

namespace App\Actions\Transactions;


use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RevertTransaction
{
    use AsAction;

    public function handle(string $transactionId): void
    {
        DB::transaction(function () use ($transactionId) {
            $transaction = Transaction::with(['user', 'product'])
                ->lockForUpdate()
                ->findOrFail($transactionId);

            // Prevent reverting already reverted transactions
            if ($transaction->reverted_at !== null) {
                throw new \Exception("Transaction already reverted");
            }

            // Lock related records
            $user = $transaction->user()->lockForUpdate()->first();
            $product = $transaction->product()->lockForUpdate()->first();

            // Refund user balance
            $user->debitBalance($transaction->total);

            // Restore product quantity
            $product->increment('quantity');

            // Mark as reverted instead of deleting (audit trail)
            $transaction->update(['reverted_at' => now()]);

            DB::afterCommit(function () use ($user) {
                Cache::decrement("analytics:transaction:amount:{$user->id}");
            });
        });
    }
}
