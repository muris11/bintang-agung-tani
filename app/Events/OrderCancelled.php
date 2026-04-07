<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public ?string $reason;

    public ?int $cancelledBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, ?string $reason = null, ?int $cancelledBy = null)
    {
        $this->order = $order;
        $this->reason = $reason;
        $this->cancelledBy = $cancelledBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.'.$this->order->id),
            new PrivateChannel('user.'.$this->order->user_id),
            new PrivateChannel('admin.orders'), // Admin channel
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.cancelled';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'reason' => $this->reason,
            'cancelled_by' => $this->cancelledBy,
            'cancelled_by_name' => $this->cancelledBy ? ($this->cancelledBy === $this->order->user_id ? 'User' : 'Admin') : null,
            'cancelled_at' => now()->toIso8601String(),
        ];
    }
}
