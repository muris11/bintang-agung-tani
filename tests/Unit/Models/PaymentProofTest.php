<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_payment_proof_with_relationships(): void
    {
        $order = Order::factory()->create();
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $paymentProof = PaymentProof::factory()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
        ]);

        $this->assertInstanceOf(PaymentProof::class, $paymentProof);
        $this->assertEquals($order->id, $paymentProof->order_id);
        $this->assertEquals($user->id, $paymentProof->user_id);
        $this->assertEquals($paymentMethod->id, $paymentProof->payment_method_id);
    }

    public function test_payment_proof_has_order_relationship(): void
    {
        $order = Order::factory()->create();
        $paymentProof = PaymentProof::factory()->create([
            'order_id' => $order->id,
        ]);

        $this->assertInstanceOf(Order::class, $paymentProof->order);
        $this->assertEquals($order->id, $paymentProof->order->id);
    }

    public function test_payment_proof_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $paymentProof = PaymentProof::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $paymentProof->user);
        $this->assertEquals($user->id, $paymentProof->user->id);
    }

    public function test_payment_proof_has_payment_method_relationship(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();
        $paymentProof = PaymentProof::factory()->create([
            'payment_method_id' => $paymentMethod->id,
        ]);

        $this->assertInstanceOf(PaymentMethod::class, $paymentProof->paymentMethod);
        $this->assertEquals($paymentMethod->id, $paymentProof->paymentMethod->id);
    }

    public function test_payment_proof_has_verifier_relationship(): void
    {
        $verifier = User::factory()->create(['is_admin' => true]);
        $paymentProof = PaymentProof::factory()->create([
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $paymentProof->verifier);
        $this->assertEquals($verifier->id, $paymentProof->verifier->id);
    }

    public function test_can_get_image_url(): void
    {
        Storage::fake('public');

        $paymentProof = PaymentProof::factory()->create([
            'image_path' => 'payment-proofs/test-image.jpg',
        ]);

        $url = $paymentProof->getImageUrl();

        $this->assertStringContainsString('payment-proofs/test-image.jpg', $url);
    }

    public function test_get_status_label_returns_correct_label(): void
    {
        $pending = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);
        $verified = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);
        $rejected = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_REJECTED]);

        $this->assertEquals('Menunggu Verifikasi', $pending->getStatusLabel());
        $this->assertEquals('Terverifikasi', $verified->getStatusLabel());
        $this->assertEquals('Ditolak', $rejected->getStatusLabel());
    }

    public function test_get_status_color_returns_correct_color(): void
    {
        $pending = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);
        $verified = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);
        $rejected = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_REJECTED]);

        $this->assertEquals('yellow', $pending->getStatusColor());
        $this->assertEquals('green', $verified->getStatusColor());
        $this->assertEquals('red', $rejected->getStatusColor());
    }

    public function test_is_pending_returns_true_for_pending_status(): void
    {
        $paymentProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);

        $this->assertTrue($paymentProof->isPending());
        $this->assertFalse($paymentProof->isVerified());
        $this->assertFalse($paymentProof->isRejected());
    }

    public function test_is_verified_returns_true_for_verified_status(): void
    {
        $paymentProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);

        $this->assertFalse($paymentProof->isPending());
        $this->assertTrue($paymentProof->isVerified());
        $this->assertFalse($paymentProof->isRejected());
    }

    public function test_is_rejected_returns_true_for_rejected_status(): void
    {
        $paymentProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_REJECTED]);

        $this->assertFalse($paymentProof->isPending());
        $this->assertFalse($paymentProof->isVerified());
        $this->assertTrue($paymentProof->isRejected());
    }

    public function test_can_mark_as_verified(): void
    {
        $verifier = User::factory()->create(['is_admin' => true]);
        $paymentProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);

        $paymentProof->markAsVerified($verifier->id, 'Payment verified successfully');

        $this->assertEquals(PaymentProof::STATUS_VERIFIED, $paymentProof->fresh()->status);
        $this->assertEquals($verifier->id, $paymentProof->fresh()->verified_by);
        $this->assertNotNull($paymentProof->fresh()->verified_at);
        $this->assertEquals('Payment verified successfully', $paymentProof->fresh()->admin_notes);
    }

    public function test_can_mark_as_rejected(): void
    {
        $verifier = User::factory()->create(['is_admin' => true]);
        $paymentProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);

        $paymentProof->markAsRejected($verifier->id, 'Image unclear, please resubmit');

        $this->assertEquals(PaymentProof::STATUS_REJECTED, $paymentProof->fresh()->status);
        $this->assertEquals($verifier->id, $paymentProof->fresh()->verified_by);
        $this->assertNotNull($paymentProof->fresh()->verified_at);
        $this->assertEquals('Image unclear, please resubmit', $paymentProof->fresh()->admin_notes);
    }

    public function test_pending_scope_returns_only_pending_proofs(): void
    {
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_REJECTED]);

        $pendingCount = PaymentProof::pending()->count();

        $this->assertEquals(1, $pendingCount);
    }

    public function test_verified_scope_returns_only_verified_proofs(): void
    {
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_REJECTED]);

        $verifiedCount = PaymentProof::verified()->count();

        $this->assertEquals(1, $verifiedCount);
    }

    public function test_rejected_scope_returns_only_rejected_proofs(): void
    {
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);
        PaymentProof::factory()->create(['status' => PaymentProof::STATUS_REJECTED]);

        $rejectedCount = PaymentProof::rejected()->count();

        $this->assertEquals(1, $rejectedCount);
    }
}
