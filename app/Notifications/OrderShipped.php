<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    private Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->order->order_number;
        $courier = $this->order->shipping_courier;
        $tracking = $this->order->tracking_number;

        $message = (new MailMessage)
            ->subject("Pesanan #{$orderNumber} Sedang Dikirim")
            ->greeting("Halo {$notifiable->name},")
            ->line('Pesanan Anda sedang dalam perjalanan!')
            ->line("Nomor Pesanan: #{$orderNumber}")
            ->line("Kurir: {$courier}");

        if ($tracking) {
            $message->line("Nomor Resi: {$tracking}");
        }

        return $message
            ->action('Lihat Pesanan', route('user.orders.show', $this->order))
            ->line('Pesanan Anda akan segera tiba.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'shipping_courier' => $this->order->shipping_courier,
            'tracking_number' => $this->order->tracking_number,
            'status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'shipping_courier' => $this->order->shipping_courier,
            'tracking_number' => $this->order->tracking_number,
            'status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'action_url' => route('user.orders.show', $this->order),
            'action_text' => 'Lacak Pesanan',
        ]);
    }
}
