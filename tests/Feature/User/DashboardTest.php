<?php

namespace Tests\Feature\User;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    protected function createProduct(array $overrides = []): Product
    {
        $category = Category::factory()->create();

        return Product::factory()->create(array_merge([
            'category_id' => $category->id,
            'is_active' => true,
            'stock' => 10,
        ], $overrides));
    }

    protected function createOrder(User $user, array $overrides = []): Order
    {
        return Order::factory()->create(array_merge([
            'user_id' => $user->id,
        ], $overrides));
    }

    public function test_user_can_view_dashboard(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewIs('user.dashboard');
    }

    public function test_dashboard_shows_cart_count(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Add items to cart
        $cart = Cart::getOrCreateForUser($user->id);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $cart->recalculateTotals();

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('cartCount', 3);
    }

    public function test_dashboard_shows_pending_payment_count(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Create orders with different statuses
        $pendingOrder = $this->createOrder($user, [
            'status' => Order::STATUS_PENDING,
        ]);
        $completedOrder = $this->createOrder($user, [
            'status' => Order::STATUS_COMPLETED,
        ]);

        // Add order items
        OrderItem::factory()->create([
            'order_id' => $pendingOrder->id,
            'product_id' => $product->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $completedOrder->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('pendingPaymentCount', 1);
    }

    public function test_dashboard_shows_processing_count(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Create processing orders
        $processingOrder1 = $this->createOrder($user, [
            'status' => Order::STATUS_PROCESSING,
        ]);
        $processingOrder2 = $this->createOrder($user, [
            'status' => Order::STATUS_PROCESSING,
        ]);

        OrderItem::factory()->create([
            'order_id' => $processingOrder1->id,
            'product_id' => $product->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $processingOrder2->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('processingCount', 2);
    }

    public function test_dashboard_shows_total_spent_this_month(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Create completed orders this month
        $order1 = $this->createOrder($user, [
            'status' => Order::STATUS_COMPLETED,
            'total_amount' => 100000,
            'created_at' => Carbon::now(),
        ]);
        $order2 = $this->createOrder($user, [
            'status' => Order::STATUS_COMPLETED,
            'total_amount' => 150000,
            'created_at' => Carbon::now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $order1->id,
            'product_id' => $product->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order2->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalSpentThisMonth', 250000);
    }

    public function test_dashboard_shows_pending_payment_total(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Create pending payment orders
        $order1 = $this->createOrder($user, [
            'status' => Order::STATUS_PENDING,
            'total_amount' => 50000,
        ]);
        $order2 = $this->createOrder($user, [
            'status' => Order::STATUS_PENDING,
            'total_amount' => 75000,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order1->id,
            'product_id' => $product->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order2->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('pendingPaymentTotal', 125000);
    }

    public function test_dashboard_shows_recent_orders(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Create 5 orders
        $orders = [];
        for ($i = 0; $i < 5; $i++) {
            $order = $this->createOrder($user, [
                'created_at' => Carbon::now()->subDays($i),
            ]);
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
            ]);
            $orders[] = $order;
        }

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $recentOrders = $response->viewData('recentOrders');

        // Should show last 3 orders
        $this->assertCount(3, $recentOrders);

        // Most recent should be first
        $this->assertEquals($orders[0]->id, $recentOrders->first()->id);
    }

    public function test_dashboard_shows_weekly_purchases_data(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Create orders for last 6 weeks
        for ($i = 0; $i < 6; $i++) {
            $order = $this->createOrder($user, [
                'status' => Order::STATUS_COMPLETED,
                'total_amount' => 100000,
                'created_at' => Carbon::now()->subWeeks($i),
            ]);
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('weeklyPurchases');
        $response->assertViewHas('weekLabels');

        $weeklyPurchases = $response->viewData('weeklyPurchases');
        $weekLabels = $response->viewData('weekLabels');

        // Should have 6 weeks of data
        $this->assertCount(6, $weeklyPurchases);
        $this->assertCount(6, $weekLabels);
    }

    public function test_dashboard_calculates_growth_percentage(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Previous month order
        $prevOrder = $this->createOrder($user, [
            'status' => Order::STATUS_COMPLETED,
            'total_amount' => 100000,
            'created_at' => Carbon::now()->subMonth(),
        ]);

        // Current month order
        $currentOrder = $this->createOrder($user, [
            'status' => Order::STATUS_COMPLETED,
            'total_amount' => 150000,
            'created_at' => Carbon::now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $prevOrder->id,
            'product_id' => $product->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $currentOrder->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $growthPercentage = $response->viewData('growthPercentage');

        // Growth should be 50% ((150k - 100k) / 100k * 100)
        $this->assertEquals(50.0, $growthPercentage);
    }

    public function test_dashboard_shows_recommended_products(): void
    {
        $user = $this->createRegularUser();

        // Create active products
        $product1 = $this->createProduct([
            'name' => 'Produk 1',
            'images' => json_encode(['image1.jpg']),
        ]);
        $product2 = $this->createProduct([
            'name' => 'Produk 2',
            'images' => json_encode(['image2.jpg']),
        ]);
        $product3 = $this->createProduct([
            'name' => 'Produk 3',
            'featured_image' => 'featured.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('recommendedProducts');

        $recommendedProducts = $response->viewData('recommendedProducts');

        // Should have recommended products
        $this->assertGreaterThan(0, $recommendedProducts->count());
    }

    public function test_dashboard_only_shows_user_own_data(): void
    {
        $user = $this->createRegularUser();
        $otherUser = User::factory()->create(['is_admin' => false]);
        $product = $this->createProduct();

        // Create orders for both users
        $userOrder = $this->createOrder($user, [
            'status' => Order::STATUS_PENDING,
            'total_amount' => 50000,
        ]);
        $otherUserOrder = $this->createOrder($otherUser, [
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100000,
        ]);

        OrderItem::factory()->create([
            'order_id' => $userOrder->id,
            'product_id' => $product->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $otherUserOrder->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();

        // Should only count user's orders
        $pendingPaymentCount = $response->viewData('pendingPaymentCount');
        $this->assertEquals(1, $pendingPaymentCount);

        // Pending total should only include user's orders
        $pendingPaymentTotal = $response->viewData('pendingPaymentTotal');
        $this->assertEquals(50000, $pendingPaymentTotal);
    }

    public function test_dashboard_handles_no_orders_gracefully(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('pendingPaymentCount', 0);
        $response->assertViewHas('processingCount', 0);
        $response->assertViewHas('totalSpentThisMonth', 0);
        $response->assertViewHas('pendingPaymentTotal', 0);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('user.dashboard'));

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_user_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('user.dashboard'));

        $response->assertRedirect('/admin/dashboard');
    }
}
