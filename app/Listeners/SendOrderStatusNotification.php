<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\LogActivity;
use App\Jobs\SendOrderStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        // Queue notification job
        SendOrderStatusUpdatedNotification::dispatch(
            $event->order,
            $event->previousStatus,
            $event->notes
        );

        // Log activity asynchronously
        LogActivity::dispatch(
            'order_status_changed',
            'Order',
            $event->order->id,
            "Order #{$event->order->order_number} status changed from {$event->previousStatus} to {$event->order->status}",
            [
                'previous_status' => $event->previousStatus,
                'current_status' => $event->order->status,
                'notes' => $event->notes,
            ],
            $event->changedBy,
            request()->ip(),
            request()->userAgent()
        );
    }
}
