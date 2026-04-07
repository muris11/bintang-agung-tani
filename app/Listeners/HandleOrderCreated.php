<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\GenerateOrderInvoice;
use App\Jobs\SendOrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleOrderCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        // Send notification to user
        SendOrderCreatedNotification::dispatch($event->order);

        // Generate invoice asynchronously
        GenerateOrderInvoice::dispatch($event->order);
    }
}
