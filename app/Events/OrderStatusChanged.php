<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public string $previousStatus;

    public ?string $notes;

    public ?int $changedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $previousStatus, ?string $notes = null, ?int $changedBy = null)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
        $this->notes = $notes;
        $this->changedBy = $changedBy;
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
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.status.changed';
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
            'previous_status' => $this->previousStatus,
            'current_status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'notes' => $this->notes,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
