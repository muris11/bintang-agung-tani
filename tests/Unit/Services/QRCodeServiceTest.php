<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\User;
use App\Services\QRCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QRCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    private QRCodeService $qrCodeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrCodeService = new QRCodeService;
        Storage::fake('public');
    }

    public function test_can_generate_qr_code_for_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'qr_code_path' => null,
            'qr_code_data' => null,
        ]);

        $result = $this->qrCodeService->generateForOrder($order);

        // Check return structure
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('data', $result);

        // Refresh order to get updated values
        $order->refresh();

        // Verify order was updated
        $this->assertNotNull($order->qr_code_path);
        $this->assertNotNull($order->qr_code_data);
        $this->assertEquals($order->qr_code_path, $result['path']);
        $this->assertEquals($order->qr_code_data, $result['data']);

        // Verify file exists in storage
        Storage::disk('public')->assertExists($order->qr_code_path);

        // Verify URL is correct
        $this->assertEquals(Storage::url($order->qr_code_path), $result['url']);
    }

    public function test_can_verify_qr_code_data(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Generate QR code first
        $this->qrCodeService->generateForOrder($order);
        $order->refresh();

        // Verify valid QR data
        $isValid = $this->qrCodeService->isValidOrderData($order->qr_code_data, $order);
        $this->assertTrue($isValid);

        // Verify invalid QR data (different order)
        $otherOrder = Order::factory()->create(['user_id' => $user->id]);
        $isInvalid = $this->qrCodeService->isValidOrderData($order->qr_code_data, $otherOrder);
        $this->assertFalse($isInvalid);

        // Verify invalid QR data (malformed)
        $isMalformed = $this->qrCodeService->isValidOrderData('invalid-json', $order);
        $this->assertFalse($isMalformed);
    }

    public function test_get_order_from_qr_data(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Generate QR code first
        $this->qrCodeService->generateForOrder($order);
        $order->refresh();

        // Get order from QR data
        $foundOrder = $this->qrCodeService->getOrderFromQrData($order->qr_code_data);

        $this->assertInstanceOf(Order::class, $foundOrder);
        $this->assertEquals($order->id, $foundOrder->id);
        $this->assertEquals($order->order_number, $foundOrder->order_number);

        // Test with invalid data
        $notFound = $this->qrCodeService->getOrderFromQrData('invalid-json');
        $this->assertNull($notFound);

        // Test with non-existent order id
        $notFoundOrder = $this->qrCodeService->getOrderFromQrData(json_encode(['order_id' => 99999]));
        $this->assertNull($notFoundOrder);
    }

    public function test_delete_qr_code(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Generate QR code first
        $this->qrCodeService->generateForOrder($order);
        $order->refresh();

        // Store the path before deletion
        $qrPath = $order->qr_code_path;

        // Verify file exists
        Storage::disk('public')->assertExists($qrPath);

        // Delete QR code
        $this->qrCodeService->deleteQrCode($order);

        // Refresh order
        $order->refresh();

        // Verify order columns are cleared
        $this->assertNull($order->qr_code_path);
        $this->assertNull($order->qr_code_data);

        // Verify file is deleted
        Storage::disk('public')->assertMissing($qrPath);
    }

    public function test_regenerate_creates_new_qr_and_deletes_old(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Generate first QR code
        $result1 = $this->qrCodeService->generateForOrder($order);
        $order->refresh();

        $oldPath = $order->qr_code_path;
        $oldData = $order->qr_code_data;

        // Wait a moment to ensure different timestamps
        sleep(1);

        // Regenerate QR code
        $result2 = $this->qrCodeService->regenerateForOrder($order);
        $order->refresh();

        // Verify new QR code is different
        $this->assertNotEquals($oldPath, $order->qr_code_path);
        $this->assertNotEquals($oldData, $order->qr_code_data);

        // Verify old file is deleted
        Storage::disk('public')->assertMissing($oldPath);

        // Verify new file exists
        Storage::disk('public')->assertExists($order->qr_code_path);

        // Verify both return structures have all required keys
        $this->assertArrayHasKey('path', $result1);
        $this->assertArrayHasKey('url', $result1);
        $this->assertArrayHasKey('data', $result1);
        $this->assertArrayHasKey('path', $result2);
        $this->assertArrayHasKey('url', $result2);
        $this->assertArrayHasKey('data', $result2);
    }
}
