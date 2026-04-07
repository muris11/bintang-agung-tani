<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\PaymentVerified as PaymentVerifiedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentVerifiedNotification implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public ?int $verifiedBy;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, ?int $verifiedBy = null)
    {
        $this->order = $order;
        $this->verifiedBy = $verifiedBy;
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send notification to user
        $this->order->user->notify(new PaymentVerifiedNotification($this->order));
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['notifications', 'payment', 'order:'.$this->order->id];
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'payment-verified-'.$this->order->id;
    }

    /**
     * Calculate the number of seconds to wait before retrying.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }
}
