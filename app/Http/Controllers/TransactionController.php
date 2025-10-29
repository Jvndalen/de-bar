<?php

namespace App\Http\Controllers;

use App\Actions\Transactions\ScanDrinkAction;
use App\Actions\Transactions\RevertTransaction;
use App\Actions\Transactions\RevertTreatTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function scanDrink(User $user, Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'is_drawer' => 'boolean',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $result = ScanDrinkAction::run(
            $user,
            $product,
            $validated['is_drawer'] ?? false
        );

        if (!$result['success']) {
            return response()->json([
                'message' => $result['error']
            ], 400);
        }

        return back()->with('success', [
            'message' => $result['message'],
            'transaction_id' => $result['transaction_id'],
            'type' => $result['type'],
        ]);
    }

    public function revertTransaction(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'type' => 'required|in:standard,treat',
        ]);

        try {
            if ($validated['type'] === 'treat') {
                RevertTreatTransaction::run($validated['transaction_id']);
            } else {
                RevertTransaction::run($validated['transaction_id']);
            }

            return response()->json([
                'message' => 'Transactie ongedaan gemaakt'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
