<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\OrderStatusUpdated as OrderStatusUpdatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderStatusUpdatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public string $previousStatus;

    public ?string $notes;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $previousStatus, ?string $notes = null)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
        $this->notes = $notes;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->order->user->notify(new OrderStatusUpdatedNotification(
            $this->order,
            $this->previousStatus,
            $this->notes
        ));
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['notifications', 'order:'.$this->order->id];
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'order-status-notification-'.$this->order->id.'-'.time();
    }
}
