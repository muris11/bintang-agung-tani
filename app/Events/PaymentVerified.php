<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public ?int $verifiedBy;

    public ?string $notes;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, ?int $verifiedBy = null, ?string $notes = null)
    {
        $this->order = $order;
        $this->verifiedBy = $verifiedBy;
        $this->notes = $notes;
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
        return 'payment.verified';
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
            'verified_by' => $this->verifiedBy,
            'notes' => $this->notes,
            'verified_at' => now()->toIso8601String(),
        ];
    }
}
