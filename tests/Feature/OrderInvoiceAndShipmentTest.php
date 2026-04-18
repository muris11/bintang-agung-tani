<?php

namespace Tests\Feature;

use App\Events\OrderStatusChanged;
use App\Jobs\GenerateOrderInvoice;
use App\Jobs\LogActivity;
use App\Listeners\SendOrderStatusNotification;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class OrderInvoiceAndShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_order_invoice_job_persists_invoice_path(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => Order::STATUS_PROCESSING,
            'invoice_path' => null,
        ]);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('invoices.order', Mockery::on(function (array $data): bool {
                return isset($data['order']) && $data['order'] instanceof Order;
            }))
            ->andReturn(tap(Mockery::mock(DomPdf::class), function ($pdf): void {
                $pdf->shouldReceive('output')->once()->andReturn('pdf-bytes');
            }));

        (new GenerateOrderInvoice($order))->handle();

        $order->refresh();

        $this->assertNotNull($order->invoice_path);
        Storage::disk('public')->assertExists($order->invoice_path);
    }

    public function test_processing_order_completion_dispatches_activity_job(): void
    {
        Bus::fake();

        $order = Order::factory()->create([
            'status' => Order::STATUS_PROCESSING,
        ]);

        app(\App\Services\OrderService::class)->updateStatus(
            $order,
            Order::STATUS_COMPLETED,
            'Pesanan selesai setelah scan barcode',
            null
        );

        Bus::assertDispatched(LogActivity::class);
        $this->assertEquals(Order::STATUS_COMPLETED, $order->fresh()->status);
    }

    public function test_order_status_changed_listener_sends_updated_status_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => Order::STATUS_PROCESSING,
        ]);

        $event = new OrderStatusChanged($order, Order::STATUS_MENUNGGU_VERIFIKASI, 'Pesanan selesai', null);

        (new SendOrderStatusNotification())->handle($event);

        Notification::assertCount(1);
    }
}
