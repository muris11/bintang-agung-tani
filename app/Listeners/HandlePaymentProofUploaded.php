<?php

namespace App\Listeners;

use App\Events\PaymentProofUploaded;
use App\Jobs\SendPaymentProofUploadedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentProofUploaded implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentProofUploaded $event): void
    {
        // Send notification to admin
        SendPaymentProofUploadedNotification::dispatch($event->order, $event->paymentProof);
    }
}
