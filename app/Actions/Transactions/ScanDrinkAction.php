<?php

namespace App\Actions\Transactions;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TreatBalance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
class ScanDrinkAction
{
    use AsAction;

    public function handle(User $user, Product $product, bool $isDrawer = false): array
    {
        $priceInCents = (int) ($product->price);

        // Step 1: Check for active treat balance
        $activeTreatBalance = TreatBalance::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('remaining_amount', '>=', $priceInCents)
            ->first();

        // Path 1: Pay with treat balance
        if ($activeTreatBalance) {
            return $this->processTreatTransaction($user, $product, $priceInCents, $activeTreatBalance, $isDrawer);
        }

        // Path 2: Pay with main balance
        return $this->processStandardTransaction($user, $product, $priceInCents, $isDrawer);
    }

    protected function processTreatTransaction(
        User $user,
        Product $product,
        int $priceInCents,
        TreatBalance $treatBalance,
        bool $isDrawer
    ): array {
        try {
            $transactionId = RegisterTreatTransaction::run(
                $user->id,
                $product->id,
                $priceInCents,
                $treatBalance->id
            );

            return [
                'success' => true,
                'type' => 'treat',
                'transaction_id' => $transactionId,
                'message' => $isDrawer
                    ? "{$product->name} voor {$user->name} besteld (uit de pot)"
                    : "Je hebt een {$product->name} besteld (uit je pot)",
            ];
        } catch (InsufficientStockException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        } catch (InsufficientBalanceException $e) {
            return [
                'success' => false,
                'error' => $isDrawer
                    ? "{$user->name} heeft niet genoeg trakteer saldo"
                    : "Je hebt niet genoeg trakteer saldo",
            ];
        }
    }

    protected function processStandardTransaction(
        User $user,
        Product $product,
        int $priceInCents,
        bool $isDrawer
    ): array {
        if ($user->rawBalance() < $priceInCents) {
            return [
                'success' => false,
                'error' => $isDrawer
                    ? "{$user->name} heeft niet genoeg saldo"
                    : "Je hebt niet genoeg saldo",
            ];
        }

        try {
            $transactionId = RegisterTransaction::run(
                $user->id,
                $product->id,
                $priceInCents
            );

            return [
                'success' => true,
                'type' => 'standard',
                'transaction_id' => $transactionId,
                'message' => $isDrawer
                    ? "{$product->name} voor {$user->name} besteld"
                    : "Je hebt een {$product->name} besteld",
            ];
        } catch (InsufficientStockException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        } catch (InsufficientBalanceException $e) {
            return [
                'success' => false,
                'error' => $isDrawer
                    ? "{$user->name} heeft niet genoeg saldo"
                    : "Je hebt niet genoeg saldo",
            ];
        }
    }
}
