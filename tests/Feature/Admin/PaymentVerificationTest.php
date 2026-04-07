<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
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

    public function test_admin_can_view_payment_proofs_list(): void
    {
        $admin = $this->createAdminUser();
        $proofs = PaymentProof::factory()->count(3)->create();

        // GET /payment-proofs now redirects to consolidated /verifikasi route
        $response = $this->actingAs($admin)->get('/admin/payment-proofs');

        $response->assertRedirect(route('admin.verifikasi.index'));
    }

    public function test_admin_can_filter_payment_proofs_by_status(): void
    {
        $admin = $this->createAdminUser();
        $pendingProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_PENDING]);
        $verifiedProof = PaymentProof::factory()->create(['status' => PaymentProof::STATUS_VERIFIED]);

        // GET /payment-proofs now redirects to consolidated /verifikasi route
        $response = $this->actingAs($admin)->get('/admin/payment-proofs?status=pending');

        $response->assertRedirect(route('admin.verifikasi.index'));
    }

    public function test_admin_can_filter_payment_proofs_by_date_range(): void
    {
        $admin = $this->createAdminUser();
        PaymentProof::factory()->create(['created_at' => now()->subDays(5)]);
        PaymentProof::factory()->create(['created_at' => now()->subDays(1)]);

        // GET /payment-proofs now redirects to consolidated /verifikasi route
        $response = $this->actingAs($admin)->get('/admin/payment-proofs?date_from='.now()->subDays(3)->format('Y-m-d').'&date_to='.now()->format('Y-m-d'));

        $response->assertRedirect(route('admin.verifikasi.index'));
    }

    public function test_admin_can_view_payment_proof_detail(): void
    {
        $admin = $this->createAdminUser();
        $proof = PaymentProof::factory()->create();

        // GET /payment-proofs/{id} now redirects to consolidated /verifikasi route
        $response = $this->actingAs($admin)->get("/admin/payment-proofs/{$proof->id}");

        $response->assertRedirect(route('admin.verifikasi.index'));
    }

    public function test_admin_can_verify_payment_proof(): void
    {
        $admin = $this->createAdminUser();
        $user = $this->createRegularUser();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);
        $paymentMethod = PaymentMethod::factory()->create();
        $proof = PaymentProof::factory()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post("/admin/payment-proofs/{$proof->id}/verify", [
            'notes' => 'Bukti pembayaran valid',
        ]);

        $response->assertRedirect();
        $proof->refresh();
        $this->assertEquals(PaymentProof::STATUS_VERIFIED, $proof->status);
        $this->assertEquals($admin->id, $proof->verified_by);
    }

    public function test_admin_cannot_verify_already_verified_proof(): void
    {
        $admin = $this->createAdminUser();
        $proof = PaymentProof::factory()->create([
            'status' => PaymentProof::STATUS_VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post("/admin/payment-proofs/{$proof->id}/verify", [
            'notes' => 'Coba verify lagi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_reject_payment_proof(): void
    {
        $admin = $this->createAdminUser();
        $user = $this->createRegularUser();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);
        $paymentMethod = PaymentMethod::factory()->create();
        $proof = PaymentProof::factory()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post("/admin/payment-proofs/{$proof->id}/reject", [
            'reason' => 'Bukti pembayaran tidak jelas',
        ]);

        $response->assertRedirect();
        $proof->refresh();
        $this->assertEquals(PaymentProof::STATUS_REJECTED, $proof->status);
        $this->assertEquals($admin->id, $proof->verified_by);
    }

    public function test_admin_cannot_reject_already_processed_proof(): void
    {
        $admin = $this->createAdminUser();
        $proof = PaymentProof::factory()->create([
            'status' => PaymentProof::STATUS_VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post("/admin/payment-proofs/{$proof->id}/reject", [
            'reason' => 'Coba reject lagi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_reject_requires_reason(): void
    {
        $admin = $this->createAdminUser();
        $proof = PaymentProof::factory()->create([
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->post("/admin/payment-proofs/{$proof->id}/reject", [
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_non_admin_cannot_access_payment_verifications(): void
    {
        $user = $this->createRegularUser();

        // GET /payment-proofs redirects to consolidated route, then non-admin is redirected to user dashboard
        $response = $this->actingAs($user)->get('/admin/payment-proofs');

        $response->assertRedirect('/user/dashboard');
    }
}
