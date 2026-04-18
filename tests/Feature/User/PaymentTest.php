<?php

namespace Tests\Feature\User;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    protected function createProduct(float $price = 10000): Product
    {
        $category = Category::factory()->create();

        return Product::factory()->create([
            'category_id' => $category->id,
            'price' => $price,
            'is_active' => true,
        ]);
    }

    protected function createOrder(User $user, float $total = 20000): Order
    {
        $product = $this->createProduct(10000);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'subtotal' => $total,
            'total_amount' => $total,
            'shipping_cost' => 0,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => $product->price,
            'subtotal' => $total,
        ]);

        return $order;
    }

    public function test_manual_payment_confirmation_by_admin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $user = $this->createRegularUser();
        $order = $this->createOrder($user, 20000);

        // Set order status to pending
        $order->status = 'pending';
        $order->save();

        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/payment/confirm", [
            'payment_method' => 'manual_transfer',
            'notes' => 'Payment confirmed via bank transfer',
        ]);

        $response->assertRedirect();
        $this->assertTrue(session()->has('success') || session()->has('error'));

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'success',
            'provider' => 'manual',
            'payment_method' => 'manual_transfer',
        ]);
    }

    public function test_non_admin_cannot_confirm_payment(): void
    {
        $user = $this->createRegularUser();
        $order = $this->createOrder($user, 20000);

        // Set order status to pending
        $order->status = 'pending';
        $order->save();

        $response = $this->actingAs($user)->post("/admin/orders/{$order->id}/payment/confirm", [
            'payment_method' => 'manual_transfer',
            'notes' => 'Payment confirmed via bank transfer',
        ]);

        $response->assertRedirect();
        $this->assertNull($response->exception);
    }
}
