<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
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
