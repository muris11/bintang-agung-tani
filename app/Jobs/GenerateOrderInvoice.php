<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateOrderInvoice implements ShouldQueue
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
        // Load order with relationships
        $order = $this->order->load(['items.product', 'user', 'statusHistories']);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.order', compact('order'));

        // Store PDF
        $filename = "invoice-{$order->order_number}.pdf";
        $path = "invoices/{$order->user_id}/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        // Update order with invoice path
        $order->update([
            'invoice_path' => $path,
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['invoices', 'order:'.$this->order->id];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(5);
    }
}
