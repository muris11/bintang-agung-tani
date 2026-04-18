<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\PaymentMethod;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderBarcodeVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Address $address;
    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->address = Address::factory()->create(['user_id' => $this->user->id]);
        $this->paymentMethod = PaymentMethod::factory()->create([
            'name' => 'Bank Transfer',
            'is_active' => true,
        ]);
    }

    /**
     * Test that barcode is NOT visible when order is in pending status
     */
    public function test_barcode_not_visible_for_pending_status(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100000,
            'qr_code_path' => 'qr-codes/test.png',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $order));

        $response->assertOk();
        // Barcode button should NOT be visible
        $response->assertDontSee('Lihat Barcode QR', false);
        $response->assertDontSee('Barang Siap Diambil!', false);
        // Should show waiting for payment message instead
        $response->assertSee('Belum Bayar', false);
    }

    /**
     * Test that barcode is NOT visible when order is in menunggu_verifikasi status
     */
    public function test_barcode_not_visible_for_menunggu_verifikasi_status(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
            'total_amount' => 100000,
            'paid_amount' => 100000,
            'qr_code_path' => 'qr-codes/test.png',
        ]);

        // Create payment proof
        PaymentProof::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $order));

        $response->assertOk();
        // Barcode button should NOT be visible yet - waiting for admin verification
        $response->assertDontSee('Lihat Barcode QR', false);
        $response->assertDontSee('Barang Siap Diambil!', false);
        // Should show waiting for verification message
        $response->assertSee('Menunggu Verifikasi', false);
        $response->assertSee('sedang diverifikasi oleh admin', false);
    }

    /**
     * Test that barcode IS visible when order is in processing status (admin verified)
     */
    public function test_barcode_visible_for_processing_status(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => Order::STATUS_PROCESSING,
            'total_amount' => 100000,
            'paid_amount' => 100000,
            'qr_code_path' => 'qr-codes/test.png',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $order));

        $response->assertOk();
        // Barcode button SHOULD be visible now
        $response->assertSee('Lihat Barcode QR', false);
        $response->assertSee('Barang Siap Diambil!', false);
        $response->assertSee('Diproses', false);
    }


    /**
     * Test that barcode IS visible for completed status
     */
    public function test_barcode_visible_for_completed_status(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => Order::STATUS_COMPLETED,
            'total_amount' => 100000,
            'paid_amount' => 100000,
            'qr_code_path' => 'qr-codes/test.png',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $order));

        $response->assertOk();
        $response->assertSee('Lihat Barcode QR', false);
    }

    /**
     * Test that barcode is NOT visible for cancelled status
     */
    public function test_barcode_not_visible_for_cancelled_status(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => Order::STATUS_CANCELLED,
            'total_amount' => 100000,
            'qr_code_path' => 'qr-codes/test.png',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $order));

        $response->assertOk();
        $response->assertDontSee('Lihat Barcode QR', false);
        $response->assertDontSee('Barang Siap Diambil!', false);
    }

    /**
     * Test Order::isVerified() method returns false for unverified statuses
     */
    public function test_is_verified_returns_false_for_unverified_statuses(): void
    {
        $unverifiedStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_MENUNGGU_VERIFIKASI,
            Order::STATUS_CANCELLED,
        ];

        foreach ($unverifiedStatuses as $status) {
            $order = Order::factory()->create([
                'user_id' => $this->user->id,
                'status' => $status,
            ]);

            $this->assertFalse($order->isVerified(), "Status {$status} should not be verified");
        }
    }

    /**
     * Test Order::isVerified() method returns true for verified statuses
     */
    public function test_is_verified_returns_true_for_verified_statuses(): void
    {
        $verifiedStatuses = [
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
        ];

        foreach ($verifiedStatuses as $status) {
            $order = Order::factory()->create([
                'user_id' => $this->user->id,
                'status' => $status,
            ]);

            $this->assertTrue($order->isVerified(), "Status {$status} should be verified");
        }
    }

    /**
     * Test Order::hasPaymentProof() method
     */
    public function test_has_payment_proof_returns_true_when_proof_exists(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        PaymentProof::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($order->hasPaymentProof());
    }

    /**
     * Test Order::hasPaymentProof() returns false when no proof exists
     */
    public function test_has_payment_proof_returns_false_when_no_proof(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertFalse($order->hasPaymentProof());
    }

    /**
     * Test Order::canViewBarcode() returns correct value based on status
     */
    public function test_can_view_barcode_logic(): void
    {
        // Should NOT be able to view for these statuses
        $cannotViewStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_MENUNGGU_VERIFIKASI,
            Order::STATUS_CANCELLED,
        ];

        foreach ($cannotViewStatuses as $status) {
            $order = Order::factory()->create([
                'user_id' => $this->user->id,
                'status' => $status,
            ]);

            $this->assertFalse($order->canViewBarcode(), "Status {$status} should NOT allow viewing barcode");
        }

        // SHOULD be able to view for these statuses
        $canViewStatuses = [
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
        ];

        foreach ($canViewStatuses as $status) {
            $order = Order::factory()->create([
                'user_id' => $this->user->id,
                'status' => $status,
            ]);

            $this->assertTrue($order->canViewBarcode(), "Status {$status} SHOULD allow viewing barcode");
        }
    }

    /**
     * Test status badge shows correct text for each status
     */
    public function test_status_badge_shows_correct_text(): void
    {
        $statusMap = [
            Order::STATUS_PENDING => 'Belum Bayar',
            Order::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            Order::STATUS_PROCESSING => 'Diproses',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
        ];

        foreach ($statusMap as $status => $expectedLabel) {
            $order = Order::factory()->create([
                'user_id' => $this->user->id,
                'address_id' => $this->address->id,
                'payment_method_id' => $this->paymentMethod->id,
                'status' => $status,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('user.orders.show', $order));

            $response->assertSee($expectedLabel, false);
        }
    }

    /**
     * Test payment status shows correct badge
     */
    public function test_payment_status_badge_shows_correctly(): void
    {
        // Order with no payment - should show "Belum Bayar"
        $unpaidOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PENDING,
            'paid_amount' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $unpaidOrder));

        $response->assertSee('Belum Bayar', false);

        // Order with payment pending verification
        $pendingOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
            'total_amount' => 100000,
            'paid_amount' => 100000,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $pendingOrder));

        $response->assertSee('Menunggu Verifikasi', false);

        // Order with verified payment
        $verifiedOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PROCESSING,
            'total_amount' => 100000,
            'paid_amount' => 100000,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $verifiedOrder));

        $response->assertSee('Lunas', false);
    }
}
