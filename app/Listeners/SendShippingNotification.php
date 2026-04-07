<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Jobs\SendOrderShippedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendShippingNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderShipped $event): void
    {
        // Send shipping notification
        SendOrderShippedNotification::dispatch($event->order);
    }
}
