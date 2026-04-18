<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
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

    public function test_admin_gets_clear_feedback_after_status_update(): void
    {
        $this->actingAs($this->admin);

        // Create order with menunggu_verifikasi status so we can transition to processing
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->patch(route('admin.orders.update-status', $order), [
            'status' => Order::STATUS_PROCESSING,
            'notes' => 'Pembayaran terverifikasi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify order was updated
        $order->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);
    }

    public function test_status_update_triggers_activity_log(): void
    {
        $this->actingAs($this->admin);

        // Create order with menunggu_verifikasi status
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        // Transition to processing (valid from menunggu_verifikasi)
        $this->patch(route('admin.orders.update-status', $order), [
            'status' => Order::STATUS_PROCESSING,
            'notes' => 'Pembayaran terverifikasi',
        ]);

        // Verify activity log was created
        $this->assertDatabaseHas('activity_logs', [
            'entity_id' => $order->id,
            'entity_type' => Order::class,
            'action' => 'order_status_updated',
        ]);
    }

    public function test_admin_can_view_all_orders(): void
    {
        $this->actingAs($this->admin);

        // Create orders for multiple users
        $users = User::factory()->count(3)->create(['is_admin' => false]);

        foreach ($users as $user) {
            $order = Order::factory()->create(['user_id' => $user->id]);
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        // Create order for current user
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pesanan');
        $response->assertViewHas('orders');
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        $this->actingAs($this->admin);

        // Create orders with different statuses
        $pendingOrder = Order::factory()->pending()->create(['user_id' => $this->user->id]);
        $completedOrder = Order::factory()->completed()->create(['user_id' => $this->user->id]);
        $processingOrder = Order::factory()->processing()->create(['user_id' => $this->user->id]);

        foreach ([$pendingOrder, $completedOrder, $processingOrder] as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        // Filter by pending status
        $response = $this->get(route('admin.orders.index', ['status' => Order::STATUS_PENDING]));

        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($orders) use ($pendingOrder) {
            return $orders->contains('id', $pendingOrder->id) &&
                   $orders->count() === 1;
        });
    }

    public function test_admin_can_update_order_status(): void
    {
        $this->actingAs($this->admin);

        // Create order with menunggu_verifikasi status so we can transition to processing
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->patch(route('admin.orders.update-status', $order), [
            'status' => Order::STATUS_PROCESSING,
            'notes' => 'Pesanan sedang diproses',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => Order::STATUS_PROCESSING,
            'previous_status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);
    }

    public function test_admin_can_add_tracking_number(): void
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->processing()->create([
            'user_id' => $this->user->id,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $courier = 'JNE';
        $trackingNumber = 'JNE1234567890';

        $response = $this->post(route('admin.orders.add-tracking', $order), [
            'courier' => $courier,
            'tracking_number' => $trackingNumber,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals($courier, $order->shipping_courier);
        $this->assertEquals($trackingNumber, $order->tracking_number);
    }

    public function test_non_admin_cannot_access_admin_orders(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('admin.orders.index'));

        $response->assertRedirect();
    }

    public function test_admin_can_search_orders_by_order_number(): void
    {
        $this->actingAs($this->admin);

        // Create orders with specific order numbers
        $targetOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'order_number' => 'BAT-20240331-12345',
        ]);

        $otherOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'order_number' => 'BAT-20240331-99999',
        ]);

        foreach ([$targetOrder, $otherOrder] as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        $response = $this->get(route('admin.orders.index', ['search' => '12345']));

        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($orders) use ($targetOrder, $otherOrder) {
            return $orders->contains('id', $targetOrder->id) &&
                   ! $orders->contains('id', $otherOrder->id);
        });
    }

    public function test_admin_can_search_orders_by_user_name(): void
    {
        $this->actingAs($this->admin);

        $targetUser = User::factory()->create(['name' => 'John Doe']);
        $otherUser = User::factory()->create(['name' => 'Jane Smith']);

        $targetOrder = Order::factory()->create(['user_id' => $targetUser->id]);
        $otherOrder = Order::factory()->create(['user_id' => $otherUser->id]);

        foreach ([$targetOrder, $otherOrder] as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        $response = $this->get(route('admin.orders.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($orders) use ($targetOrder, $otherOrder) {
            return $orders->contains('id', $targetOrder->id) &&
                   ! $orders->contains('id', $otherOrder->id);
        });
    }

    public function test_admin_can_filter_orders_by_date_range(): void
    {
        $this->actingAs($this->admin);

        // Create orders at different dates
        $oldOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(10),
        ]);

        $recentOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(2),
        ]);

        foreach ([$oldOrder, $recentOrder] as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        // Filter by date range that includes only recent order
        $response = $this->get(route('admin.orders.index', [
            'date_from' => now()->subDays(5)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($orders) use ($recentOrder, $oldOrder) {
            return $orders->contains('id', $recentOrder->id) &&
                   ! $orders->contains('id', $oldOrder->id);
        });
    }

    public function test_admin_can_view_order_details(): void
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('admin.detail-pesanan');
        $response->assertViewHas('order');
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $this->actingAs($this->admin);

        // Create order with completed status
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_COMPLETED,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        // Try to transition from completed to pending (invalid)
        $response = $this->patch(route('admin.orders.update-status', $order), [
            'status' => Order::STATUS_PENDING,
            'notes' => 'Invalid transition attempt',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Status should remain completed
        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }

    public function test_tracking_number_validation(): void
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->processing()->create([
            'user_id' => $this->user->id,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        // Missing courier
        $response = $this->post(route('admin.orders.add-tracking', $order), [
            'courier' => '',
            'tracking_number' => 'JNE123456',
        ]);

        $response->assertSessionHasErrors('courier');

        // Missing tracking number
        $response = $this->post(route('admin.orders.add-tracking', $order), [
            'courier' => 'JNE',
            'tracking_number' => '',
        ]);

        $response->assertSessionHasErrors('tracking_number');
    }

    public function test_admin_can_view_bulk_update_page(): void
    {
        $this->actingAs($this->admin);

        // Create orders for bulk update
        $order1 = Order::factory()->create(['user_id' => $this->user->id, 'status' => Order::STATUS_PENDING]);
        $order2 = Order::factory()->create(['user_id' => $this->user->id, 'status' => Order::STATUS_PENDING]);

        foreach ([$order1, $order2] as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        $response = $this->get(route('admin.orders.bulk-update-status.page'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.update-status');
        $response->assertViewHas('orders');
    }

    public function test_admin_can_bulk_update_order_status(): void
    {
        $this->actingAs($this->admin);

        // Create orders with menunggu_verifikasi status
        $order1 = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);
        $order2 = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        foreach ([$order1, $order2] as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        // Note: The controller validates English status names (matching Order model constants)
        $response = $this->patch(route('admin.orders.bulk-update-status'), [
            'order_ids' => [$order1->id, $order2->id],
            'status' => Order::STATUS_PROCESSING,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify both orders were updated
        $order1->refresh();
        $order2->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order1->status);
        $this->assertEquals(Order::STATUS_PROCESSING, $order2->status);

        // Verify activity logs were created
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'bulk_order_status_updated',
        ]);
    }

    public function test_bulk_update_requires_order_ids(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.orders.bulk-update-status'), [
            'order_ids' => [],
            'status' => Order::STATUS_PROCESSING,
        ]);

        $response->assertSessionHasErrors('order_ids');
    }

    public function test_bulk_update_requires_valid_status(): void
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PENDING,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->patch(route('admin.orders.bulk-update-status'), [
            'order_ids' => [$order->id],
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_bulk_update_skips_invalid_transitions(): void
    {
        $this->actingAs($this->admin);

        // Create order with completed status (cannot transition to processing)
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_COMPLETED,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->patch(route('admin.orders.bulk-update-status'), [
            'order_ids' => [$order->id],
            'status' => Order::STATUS_PROCESSING,
        ]);

        $response->assertRedirect();
        // Order should not be updated due to invalid transition
        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }
}
