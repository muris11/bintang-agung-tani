<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use App\Notifications\PaymentProofUploaded as PaymentProofUploadedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentProofUploadedNotification implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public PaymentProof $paymentProof;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, PaymentProof $paymentProof)
    {
        $this->order = $order;
        $this->paymentProof = $paymentProof;
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send notification to admin users
        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            $admin->notify(new PaymentProofUploadedNotification($this->order, $this->paymentProof));
        }
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
        return 'payment-proof-uploaded-'.$this->order->id.'-'.$this->paymentProof->id;
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
