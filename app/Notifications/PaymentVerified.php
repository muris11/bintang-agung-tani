<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerified extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("Pembayaran Terverifikasi - Pesanan #{$orderNumber}")
            ->greeting("Halo {$notifiable->name},")
            ->line('Pembayaran Anda telah berhasil diverifikasi oleh admin.')
            ->line("Nomor Pesanan: #{$orderNumber}")
            ->line('Status: Diproses')
            ->line('Pesanan Anda sekarang sedang diproses.')
            ->action('Lihat Pesanan', route('user.orders.show', $this->order))
            ->line('Terima kasih telah berbelanja di Bintang Agung Tani.');
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
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'paid_amount' => $this->order->paid_amount,
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
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'paid_amount' => $this->order->paid_amount,
            'action_url' => route('user.orders.show', $this->order),
            'action_text' => 'Lihat Pesanan',
            'message' => 'Pembayaran Anda telah diverifikasi. Pesanan sedang diproses.',
        ]);
    }
}
