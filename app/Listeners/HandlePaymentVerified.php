<?php

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Jobs\SendPaymentVerifiedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentVerified implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentVerified $event): void
    {
        // Send notification to user
        SendPaymentVerifiedNotification::dispatch($event->order, $event->verifiedBy);
    }
}
