<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QRScanTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_view_qr_scan_page(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/scan-qr');

        $response->assertOk();
    }

    public function test_admin_can_scan_qr_and_complete_order(): void
    {
        $admin = $this->createAdminUser();
        $order = Order::factory()->create([
            'status' => Order::STATUS_PROCESSING,
        ]);
        $qrData = $order->generateQrCodeData();

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => $qrData,
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('order_id', $order->id);
        $response->assertSessionHas('order_completed', true);
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }

    public function test_admin_cannot_complete_order_not_in_processing_status(): void
    {
        $admin = $this->createAdminUser();
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
        ]);
        $qrData = $order->generateQrCodeData();

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => $qrData,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
    }

    public function test_admin_cannot_complete_order_waiting_for_verification(): void
    {
        $admin = $this->createAdminUser();
        $order = Order::factory()->create([
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);
        $qrData = $order->generateQrCodeData();

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => $qrData,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals(Order::STATUS_MENUNGGU_VERIFIKASI, $order->status);
    }

    public function test_admin_cannot_complete_order_not_in_processing_status_after_qr_scan(): void
    {
        $admin = $this->createAdminUser();
        $order = Order::factory()->create([
            'status' => Order::STATUS_COMPLETED,
        ]);
        $qrData = $order->generateQrCodeData();

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => $qrData,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }

    public function test_scan_returns_error_for_invalid_qr_data(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => 'invalid-qr-data',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_scan_returns_error_for_non_existent_order(): void
    {
        $admin = $this->createAdminUser();
        $invalidQrData = json_encode([
            'order_id' => 999999,
            'order_number' => 'BAT-20240331-XXXX',
        ]);

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => $invalidQrData,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_scan_requires_qr_data(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post('/admin/scan-qr', [
            'qr_data' => '',
        ]);

        $response->assertSessionHasErrors('qr_data');
    }

    public function test_non_admin_cannot_access_qr_scan_page(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get('/admin/scan-qr');

        $response->assertRedirect('/user/dashboard');
    }
}
