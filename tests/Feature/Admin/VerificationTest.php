<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Category $category;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->category = Category::factory()->create();

        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 50,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_pending_verifications(): void
    {
        $this->actingAs($this->admin);

        // Create orders and payments
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PAYMENT_PENDING,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'provider' => 'manual',
            'status' => Payment::STATUS_PENDING,
            'amount' => $order->total_amount,
        ]);

        $response = $this->get(route('admin.verifikasi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.verifikasi');
    }

    public function test_admin_can_approve_payment(): void
    {
        $this->actingAs($this->admin);

        // Create order and payment
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PAYMENT_PENDING,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'provider' => 'manual',
            'status' => Payment::STATUS_PENDING,
            'amount' => $order->total_amount,
        ]);

        $response = $this->post(route('admin.verifikasi.approve', $payment), [
            'notes' => 'Pembayaran diterima',
        ]);

        $response->assertRedirect(route('admin.verifikasi.index'));
        $response->assertSessionHas('success');

        // Payment should be marked as success
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_SUCCESS,
        ]);

        // Order should be updated
        $order->refresh();
        $this->assertTrue($order->isPaid());
    }

    public function test_admin_can_reject_payment(): void
    {
        $this->actingAs($this->admin);

        // Create order and payment
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PAYMENT_PENDING,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'provider' => 'manual',
            'status' => Payment::STATUS_PENDING,
            'amount' => $order->total_amount,
        ]);

        $response = $this->post(route('admin.verifikasi.reject', $payment), [
            'reason' => 'Bukti pembayaran tidak valid',
        ]);

        $response->assertRedirect(route('admin.verifikasi.index'));
        $response->assertSessionHas('success');

        // Payment should be marked as failed
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_FAILED,
        ]);
    }

    public function test_non_admin_cannot_access_verification(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('admin.verifikasi.index'));

        $response->assertRedirect('/user/dashboard');
    }

    public function test_old_payment_proofs_route_redirects_to_verifikasi(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/payment-proofs');

        $response->assertRedirect(route('admin.verifikasi.index'));
    }

    public function test_verifikasi_pembayaran_route_redirects_to_verifikasi(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/verifikasi-pembayaran');

        $response->assertRedirect(route('admin.verifikasi.index'));
    }

    public function test_admin_can_filter_by_status(): void
    {
        $this->actingAs($this->admin);

        // Create orders and payments with different statuses
        $order1 = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory()->create(['order_id' => $order1->id, 'product_id' => $this->product->id]);
        Payment::factory()->create([
            'order_id' => $order1->id,
            'user_id' => $this->user->id,
            'provider' => 'manual',
            'status' => Payment::STATUS_PENDING,
        ]);

        $order2 = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory()->create(['order_id' => $order2->id, 'product_id' => $this->product->id]);
        Payment::factory()->create([
            'order_id' => $order2->id,
            'user_id' => $this->user->id,
            'provider' => 'manual',
            'status' => Payment::STATUS_SUCCESS,
        ]);

        // Test pending filter
        $response = $this->get(route('admin.verifikasi.index', ['status' => 'pending']));
        $response->assertStatus(200);

        // Test verified filter
        $response = $this->get(route('admin.verifikasi.index', ['status' => 'verified']));
        $response->assertStatus(200);

        // Test rejected filter
        $response = $this->get(route('admin.verifikasi.index', ['status' => 'rejected']));
        $response->assertStatus(200);
    }
}
