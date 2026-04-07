<?php

namespace App\Events;

use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProofUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public PaymentProof $paymentProof;

    public ?int $uploadedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, PaymentProof $paymentProof, ?int $uploadedBy = null)
    {
        $this->order = $order;
        $this->paymentProof = $paymentProof;
        $this->uploadedBy = $uploadedBy;
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
            new PrivateChannel('admin.notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'payment-proof.uploaded';
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
            'payment_proof_id' => $this->paymentProof->id,
            'uploaded_by' => $this->uploadedBy,
            'uploaded_at' => now()->toIso8601String(),
            'message' => 'Bukti pembayaran baru menunggu verifikasi',
        ];
    }
}
