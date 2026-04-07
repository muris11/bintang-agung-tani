<?php

namespace Tests\Feature\User;

use App\Models\Cart;
use App\Models\CartItem;
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

    private User $user;

    private Category $category;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_user_can_create_order_from_cart(): void
    {
        $this->actingAs($this->user);

        // Create cart with items
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => $this->product->price,
            'subtotal' => $this->product->price * 5,
        ]);

        $orderData = [
            'shipping_address' => 'Jl. Mawar No. 123, Jakarta',
            'shipping_phone' => '081234567890',
            'notes' => 'Tolong hati-hati',
        ];

        // Since CheckoutController doesn't exist yet, we'll test via OrderService directly
        $orderService = app(\App\Services\OrderService::class);
        $order = $orderService->createFromCart($this->user, $orderData);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);
    }

    public function test_order_decreases_product_stock(): void
    {
        $this->actingAs($this->user);

        $initialStock = $this->product->stock;
        $quantity = 10;

        // Create cart with items
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => $quantity,
            'unit_price' => $this->product->price,
            'subtotal' => $this->product->price * $quantity,
        ]);

        $orderData = [
            'shipping_address' => 'Jl. Mawar No. 123, Jakarta',
            'shipping_phone' => '081234567890',
        ];

        $orderService = app(\App\Services\OrderService::class);
        $orderService->createFromCart($this->user, $orderData);

        $this->product->refresh();
        $this->assertEquals($initialStock - $quantity, $this->product->stock);
    }

    public function test_order_cannot_be_created_with_empty_cart(): void
    {
        $this->actingAs($this->user);

        // Create empty cart
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);

        $orderData = [
            'shipping_address' => 'Jl. Mawar No. 123, Jakarta',
            'shipping_phone' => '081234567890',
        ];

        $orderService = app(\App\Services\OrderService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Your cart is empty');

        $orderService->createFromCart($this->user, $orderData);
    }

    public function test_order_status_can_be_updated_by_admin(): void
    {
        $this->actingAs($this->user);

        // Create an order via factory with payment_pending status
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PAYMENT_PENDING,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
        ]);

        // Add status history for payment_pending
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => Order::STATUS_PAYMENT_PENDING,
        ]);

        // Create admin and update order status from payment_pending to processing
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $orderService = app(\App\Services\OrderService::class);
        $orderService->updateStatus($order, Order::STATUS_PROCESSING, 'Pesanan diproses', $admin->id);

        $order->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => Order::STATUS_PROCESSING,
            'previous_status' => Order::STATUS_PAYMENT_PENDING,
        ]);
    }

    public function test_user_can_view_order_history(): void
    {
        $this->actingAs($this->user);

        // Create orders for user
        $orders = Order::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        foreach ($orders as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
            ]);
        }

        // Access order history page
        $response = $this->get(route('user.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('user.riwayat');
    }

    public function test_order_number_is_generated_automatically(): void
    {
        $this->actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('BAT-', $order->order_number);
        $this->assertMatchesRegularExpression('/^BAT-\d{8}-[A-Z0-9]{4}$/', $order->order_number);
    }

    public function test_cancelled_order_restores_stock(): void
    {
        $this->actingAs($this->user);

        $initialStock = $this->product->stock;
        $quantity = 5;

        // Create order with items
        $order = Order::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => $quantity,
        ]);

        // Decrease stock as if order was just created
        $this->product->decreaseStock($quantity, 'Test order created');

        $this->assertEquals($initialStock - $quantity, $this->product->fresh()->stock);

        // Cancel order
        $orderService = app(\App\Services\OrderService::class);
        $orderService->cancelOrder($order, 'Test cancellation', $this->user->id);

        $order->refresh();
        $this->assertEquals(Order::STATUS_CANCELLED, $order->status);

        // Stock should be restored
        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);
    }

    public function test_barcode_route_requires_order_parameter(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('user.barcode-pesanan', $order));

        $response->assertStatus(200);
        $response->assertViewIs('user.barcode-pesanan');
    }

    public function test_barcode_route_returns_404_without_order(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user/barcode-pesanan');

        $response->assertStatus(404);
    }

    public function test_old_barcode_routes_redirect_to_new_route(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Old route should redirect
        $response = $this->actingAs($user)->get("/user/pesanan/barcode/{$order->id}");
        $response->assertRedirect(route('user.barcode-pesanan', $order));
    }

    public function test_order_status_page_is_not_duplicate(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get(route('user.orders.show', $order));
        
        $response->assertStatus(200);
        // Should use detail-pesanan, not status-pesanan
        $response->assertViewIs('user.detail-pesanan');
    }
}
