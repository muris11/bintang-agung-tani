<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Address;
use App\Models\User;
use App\Models\PaymentProof;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Address $address;
    private PaymentMethod $paymentMethod;
    private Order $order;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
        $this->address = Address::factory()->create(['user_id' => $this->user->id]);
        $this->paymentMethod = PaymentMethod::factory()->create([
            'name' => 'Bank Transfer',
            'is_active' => true,
        ]);
    }

    private function setupOrderWithPaymentProof(): void
    {
        // Create order with menunggu_verifikasi status
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
            'total_amount' => 100000,
            'paid_amount' => 0,
        ]);

        // Create payment proof
        PaymentProof::factory()->create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'payment_method_id' => $this->paymentMethod->id,
            'image_path' => 'payment_proofs/test.jpg',
            'original_filename' => 'test.jpg',
            'status' => 'pending',
        ]);

        // Create pending payment record
        $this->payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'payment_method' => 'bank_transfer',
            'provider' => 'manual',
            'amount' => 100000,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    /**
     * Test admin can view verification list page
     */
    public function test_admin_can_view_verification_list(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifikasi.index'));

        $response->assertOk();
        $response->assertSee('Verifikasi Pembayaran', false);
        $response->assertSee($this->order->order_number, false);
    }

    /**
     * Test admin can view payment proof detail page
     */
    public function test_admin_can_view_payment_proof_detail(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifikasi.show', $this->payment));

        $response->assertOk();
        $response->assertSee('Dokumen Bukti Transfer', false);
        $response->assertSee($this->order->order_number, false);
        $response->assertSee($this->user->name, false);
    }

    /**
     * Test admin can approve payment and order status changes to processing
     */
    public function test_admin_approve_updates_order_status_to_processing(): void
    {
        $this->setupOrderWithPaymentProof();

        $this->assertEquals(Order::STATUS_MENUNGGU_VERIFIKASI, $this->order->fresh()->status);
        $this->assertTrue($this->payment->isPending());

        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Pembayaran valid',
            ]);

        // Debug if there's an error
        if (session()->has('error')) {
            $this->fail('Approval failed with error: ' . session()->get('error'));
        }

        $response->assertRedirect(route('admin.verifikasi.index'));
        $response->assertSessionHas('success');

        // Refresh order from database
        $this->order->refresh();

        // Order status should now be processing
        $this->assertEquals(Order::STATUS_PROCESSING, $this->order->status);

        // Payment should be success
        $this->payment->refresh();
        $this->assertEquals(Payment::STATUS_SUCCESS, $this->payment->status);
    }

    /**
     * Test order status history is created when admin verifies payment
     */
    public function test_status_history_created_when_payment_verified(): void
    {
        $this->setupOrderWithPaymentProof();

        $initialHistoryCount = $this->order->statusHistories()->count();

        $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Pembayaran valid',
            ]);

        $this->order->refresh();

        // New history record should be created
        $this->assertGreaterThan($initialHistoryCount, $this->order->statusHistories()->count());

        // Latest history should show status change to processing
        $latestHistory = $this->order->statusHistories()->latest()->first();
        $this->assertEquals(Order::STATUS_PROCESSING, $latestHistory->status);
        $this->assertEquals(Order::STATUS_MENUNGGU_VERIFIKASI, $latestHistory->previous_status);
    }

    /**
     * Test activity log is created when admin verifies payment
     */
    public function test_activity_log_created_when_payment_verified(): void
    {
        $this->setupOrderWithPaymentProof();

        $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Pembayaran valid',
            ]);

        // Check activity log exists
        $activityLog = ActivityLog::where('entity_type', Order::class)
            ->where('entity_id', $this->order->id)
            ->where('action', 'payment_verified')
            ->first();

        $this->assertNotNull($activityLog);
        $this->assertEquals($this->admin->id, $activityLog->user_id);
    }

    /**
     * Test paid_amount is updated when payment is verified
     */
    public function test_paid_amount_updated_when_payment_verified(): void
    {
        $this->setupOrderWithPaymentProof();

        $this->assertEquals(0, $this->order->paid_amount);

        $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Pembayaran valid',
            ]);

        $this->order->refresh();

        $this->assertEquals(100000, $this->order->paid_amount);
    }

    /**
     * Test barcode becomes visible to user after admin verification
     */
    public function test_barcode_visible_to_user_after_admin_verification(): void
    {
        $this->setupOrderWithPaymentProof();

        // Verify order is in menunggu_verifikasi status
        $this->assertEquals(Order::STATUS_MENUNGGU_VERIFIKASI, $this->order->status);

        // Before verification - user should NOT see barcode
        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $this->order));
        $response->assertDontSee('Lihat Barcode QR', false);
        $response->assertSee('Menunggu Verifikasi', false);

        // Admin verifies payment
        $approveResponse = $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Pembayaran valid',
            ]);

        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');

        // Refresh order from database
        $this->order->refresh();

        // Verify order status changed to processing
        $this->assertEquals(Order::STATUS_PROCESSING, $this->order->status);
        $this->assertTrue($this->order->isVerified());
        $this->assertTrue($this->order->canViewBarcode());

        // After verification - user SHOULD see barcode
        $response = $this->actingAs($this->user)
            ->get(route('user.orders.show', $this->order));
        $response->assertSee('Lihat Barcode QR', false);
        $response->assertSee('Barang Siap Diambil!', false);
    }

    /**
     * Test admin can reject payment
     */
    public function test_admin_can_reject_payment(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.reject', $this->payment), [
                'reason' => 'Nominal tidak sesuai',
            ]);

        $response->assertRedirect(route('admin.verifikasi.index'));
        $response->assertSessionHas('success');

        // Payment should be failed/rejected
        $this->payment->refresh();
        $this->assertEquals(Payment::STATUS_FAILED, $this->payment->status);
    }

    /**
     * Test non-admin is redirected when accessing verification
     */
    public function test_non_admin_is_redirected_from_verification(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->user)
            ->get(route('admin.verifikasi.index'));

        // Admin middleware redirects to user dashboard
        $response->assertRedirect('/user/dashboard');
    }

    /**
     * Test rejection requires reason
     */
    public function test_reject_requires_reason(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.reject', $this->payment), []);

        $response->assertSessionHasErrors('reason');
    }

    /**
     * Test cannot approve already processed payment
     */
    public function test_cannot_approve_already_processed_payment(): void
    {
        $this->setupOrderWithPaymentProof();

        // First approval
        $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Pembayaran valid',
            ]);

        // Try to approve again
        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment->fresh()), [
                'notes' => 'Trying again',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test payment proof image is displayed in admin detail view
     */
    public function test_payment_proof_image_displayed_in_admin_view(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifikasi.show', $this->payment));

        $response->assertOk();
        $response->assertSee('Lampiran Pelanggan', false);
        // Should show image or placeholder
        $response->assertSee('payment_proofs/test.jpg', false);
    }

    /**
     * Test admin sees correct status badges in verification list
     */
    public function test_admin_sees_correct_status_badges(): void
    {
        $this->setupOrderWithPaymentProof();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifikasi.index'));

        $response->assertOk();
        $response->assertSee('Pending', false); // Payment status badge
    }

    /**
     * Test verification list filter by status
     */
    public function test_verification_list_filters_by_status(): void
    {
        $this->setupOrderWithPaymentProof();

        // Pending filter
        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifikasi.index', ['status' => 'pending']));
        $response->assertOk();
        $response->assertSee($this->order->order_number, false);

        // Approve and check verified filter
        $this->actingAs($this->admin)
            ->post(route('admin.verifikasi.approve', $this->payment), [
                'notes' => 'Valid',
            ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifikasi.index', ['status' => 'verified']));
        $response->assertOk();
        $response->assertSee($this->order->order_number, false);
    }
}
