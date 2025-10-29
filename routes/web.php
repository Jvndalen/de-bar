<?php

use App\Http\Controllers\TransactionController;
use App\Models\Product;
use App\Models\TreatBalance;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::get('/transactions/{user}', function (User $user) {
    $transactions = $user->transactions()->with('product')->latest()->take(50)->get();

    return Inertia::render('transactions', [
        'user' => $user,
        'transactions' => $transactions,
    ]);
})->name('transactions');

Route::post('/transactions/{user}', [TransactionController::class, 'scanDrink'])->name('transactions.scan');

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return Inertia::render('dashboard', [
            'products' => Product::available()->get(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'balance' => $user->rawBalance(),
            ],
            'activeTreatBalance' => TreatBalance::where('user_id', $user->id)
                ->active()
                ->first(),
        ]);
    })->name('dashboard');


    Route::get('/checkout', function (Request $request) {
        $stripePriceId = getenv('STRIPE_PRICE_ID');

        return $request->user()->checkout($stripePriceId, [
            'success_url' => route('dashboard'),
            'cancel_url' => route('dashboard'),
            'metadata' => ['user_id' => $request->user()->id],
        ]);
    })->name('checkout');

    Route::get('/balance', function (Request $request) {
        $user = $request->user();
        return response()->json(['balance' => $user->rawBalance()]);
    })->name('balance');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
