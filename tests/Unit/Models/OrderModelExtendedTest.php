<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelExtendedTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_has_menunggu_verifikasi_status_constant()
    {
        $this->assertEquals('menunggu_verifikasi', Order::STATUS_MENUNGGU_VERIFIKASI);
        $this->assertArrayHasKey(Order::STATUS_MENUNGGU_VERIFIKASI, Order::STATUS_LABELS);
        $this->assertArrayHasKey(Order::STATUS_MENUNGGU_VERIFIKASI, Order::STATUS_COLORS);
    }

    public function test_order_can_have_payment_method_relationship()
    {
        $order = Order::factory()->create();
        $method = PaymentMethod::factory()->create();

        $order->update(['payment_method_id' => $method->id]);

        $this->assertEquals($method->id, $order->fresh()->payment_method_id);
        $this->assertInstanceOf(PaymentMethod::class, $order->fresh()->paymentMethod);
    }

    public function test_order_can_have_payment_proofs_relationship()
    {
        $order = Order::factory()->create();
        $user = User::factory()->create();
        $method = PaymentMethod::factory()->create();

        PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'image_path' => 'proofs/test.jpg',
            'original_filename' => 'test.jpg',
            'file_size' => 1024,
        ]);

        $this->assertInstanceOf(PaymentProof::class, $order->paymentProofs->first());
        $this->assertEquals(1, $order->paymentProofs()->count());
    }

    public function test_order_has_latest_payment_proof_relationship()
    {
        $order = Order::factory()->create();
        $user = User::factory()->create();
        $method = PaymentMethod::factory()->create();

        PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'image_path' => 'proofs/first.jpg',
            'original_filename' => 'first.jpg',
            'file_size' => 1024,
            'created_at' => now()->subDay(),
        ]);

        PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'image_path' => 'proofs/latest.jpg',
            'original_filename' => 'latest.jpg',
            'file_size' => 2048,
            'created_at' => now(),
        ]);

        $latestProof = $order->fresh()->latestPaymentProof;
        $this->assertEquals('proofs/latest.jpg', $latestProof->image_path);
    }

    public function test_order_can_generate_qr_code_data()
    {
        $order = Order::factory()->create();

        $data = $order->generateQrCodeData();

        $this->assertJson($data);
        $decoded = json_decode($data, true);
        $this->assertEquals($order->order_number, $decoded['order_number']);
        $this->assertEquals($order->id, $decoded['order_id']);
        $this->assertArrayHasKey('total', $decoded);
        $this->assertArrayHasKey('timestamp', $decoded);
    }

    public function test_order_get_qr_code_url_returns_null_when_no_path()
    {
        $order = Order::factory()->create();

        $this->assertNull($order->getQrCodeUrl());
    }

    public function test_order_is_pending_includes_menunggu_verifikasi()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);
        $this->assertTrue($order->isPending());

        $order->update(['status' => Order::STATUS_MENUNGGU_VERIFIKASI]);
        $this->assertTrue($order->fresh()->isPending());

        $order->update(['status' => Order::STATUS_PROCESSING]);
        $this->assertFalse($order->fresh()->isPending());
    }

    public function test_order_is_menunggu_verifikasi_returns_true_for_that_status()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_MENUNGGU_VERIFIKASI]);
        $this->assertTrue($order->isMenungguVerifikasi());

        $order->update(['status' => Order::STATUS_PENDING]);
        $this->assertFalse($order->fresh()->isMenungguVerifikasi());
    }

    public function test_order_can_upload_proof_returns_true_for_allowed_statuses()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);
        $this->assertTrue($order->canUploadProof());

        $order->update(['status' => Order::STATUS_MENUNGGU_VERIFIKASI]);
        $this->assertTrue($order->fresh()->canUploadProof());

        $order->update(['status' => Order::STATUS_PROCESSING]);
        $this->assertFalse($order->fresh()->canUploadProof());
    }

    public function test_order_status_label_for_menunggu_verifikasi()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_MENUNGGU_VERIFIKASI]);
        $this->assertEquals('Menunggu Verifikasi', $order->getStatusLabel());
    }

    public function test_order_status_color_for_menunggu_verifikasi()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_MENUNGGU_VERIFIKASI]);
        $this->assertEquals('orange', $order->getStatusColor());
    }
}
