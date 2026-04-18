<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreated extends Notification implements ShouldQueue
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
        $total = number_format($this->order->total_amount, 0, ',', '.');

        return (new MailMessage)
            ->subject("Pesanan #{$orderNumber} Berhasil Dibuat")
            ->greeting("Halo {$notifiable->name},")
            ->line('Pesanan Anda telah berhasil dibuat.')
            ->line("Nomor Pesanan: #{$orderNumber}")
            ->line("Total: Rp {$total}")
            ->line('Status: Belum Bayar')
            ->action('Lihat Pesanan', route('user.orders.show', $this->order))
            ->action('Upload Bukti Pembayaran', route('user.payments.select-method', $this->order))
            ->line('Silakan lakukan pembayaran dan upload bukti pembayaran Anda.');
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
            'item_count' => $this->order->items->count(),
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
            'item_count' => $this->order->items->count(),
            'action_url' => route('user.orders.show', $this->order),
            'action_text' => 'Lihat Pesanan',
        ]);
    }
}
