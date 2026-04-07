<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleOrderCancelled implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event): void
    {
        // Log cancellation
        ActivityLog::create([
            'user_id' => $event->cancelledBy,
            'action' => 'order_cancelled',
            'entity_type' => 'Order',
            'entity_id' => $event->order->id,
            'description' => "Order #{$event->order->order_number} cancelled. Reason: {$event->reason}",
            'metadata' => [
                'order_number' => $event->order->order_number,
                'reason' => $event->reason,
                'cancelled_by' => $event->cancelledBy,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
