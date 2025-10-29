<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Cashier\Events\WebhookReceived;

class HandleStripeCheckoutCompleted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'checkout.session.completed') {
            return;
        }

        $session = $event->payload['data']['object'];
        $userId = $session['metadata']['user_id'] ?? null;
        $amount = $session['amount_total'] ?? 0;

        if (!$userId || ($session['payment_status'] ?? '') !== 'paid') {
            return;
        }

        $user = User::find($userId);
        $user?->debitBalance($amount);
    }
}
