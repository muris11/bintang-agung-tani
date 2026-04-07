<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\OrderCreated as OrderCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->order->user->notify(new OrderCreatedNotification($this->order));
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
        return 'order-created-notification-'.$this->order->id;
    }
}
