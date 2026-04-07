<?php

namespace Tests\Feature\User;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    protected function createProduct(int $stock = 10, float $price = 10000): Product
    {
        $category = Category::factory()->create();

        return Product::factory()->create([
            'category_id' => $category->id,
            'stock' => $stock,
            'price' => $price,
            'is_active' => true,
        ]);
    }

    protected function createCartWithItem(User $user, Product $product, int $quantity = 1): Cart
    {
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'subtotal' => $product->price * $quantity,
        ]);

        return $cart;
    }

    public function test_user_can_checkout_from_cart(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct(10, 10000);
        $this->createCartWithItem($user, $product, 2);

        $response = $this->actingAs($user)->post('/user/checkout', [
            'shipping_address' => 'Jl. Test No. 123',
            'shipping_phone' => '081234567890',
            'notes' => 'Please handle with care',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_checkout_creates_order_with_correct_total(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct(10, 15000);
        $this->createCartWithItem($user, $product, 3);

        $shippingCost = 10000;

        $response = $this->actingAs($user)->post('/user/checkout', [
            'shipping_address' => 'Jl. Test No. 123',
            'shipping_phone' => '081234567890',
            'shipping_cost' => $shippingCost,
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
        ]);

        $response->assertRedirect();

        $expectedSubtotal = 15000 * 3; // 45000
        $expectedTotal = $expectedSubtotal + $shippingCost; // 55000

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'subtotal' => $expectedSubtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $expectedTotal,
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
        ]);
    }

    public function test_checkout_clears_cart(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct(10, 10000);
        $cart = $this->createCartWithItem($user, $product, 2);

        $response = $this->actingAs($user)->post('/user/checkout', [
            'shipping_address' => 'Jl. Test No. 123',
            'shipping_phone' => '081234567890',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }

    public function test_cannot_checkout_with_empty_cart(): void
    {
        $user = $this->createRegularUser();
        // Create empty cart
        Cart::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/user/checkout', [
            'shipping_address' => 'Jl. Test No. 123',
            'shipping_phone' => '081234567890',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', [
            'user_id' => $user->id,
        ]);
    }

    public function test_checkout_with_saved_address(): void
    {
        $user = $this->createRegularUser();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);
        $product = $this->createProduct(10, 10000);
        $this->createCartWithItem($user, $product, 2);

        $response = $this->actingAs($user)->post('/user/checkout', [
            'address_id' => $address->id,
            'notes' => 'Use saved address',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_select_different_address_at_checkout()
    {
        $user = User::factory()->create();
        $address1 = Address::factory()->create([
            'user_id' => $user->id, 
            'is_default' => true,
            'full_address' => 'Alamat Default'
        ]);
        $address2 = Address::factory()->create([
            'user_id' => $user->id, 
            'is_default' => false,
            'full_address' => 'Alamat Alternatif'
        ]);
        
        // Add item to cart
        $product = Product::factory()->create(['stock' => 10, 'price' => 50000, 'is_active' => true]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 50000,
        ]);
        
        // Checkout with non-default address
        $response = $this->actingAs($user)->post(route('user.checkout.store'), [
            'address_id' => $address2->id,
            'shipping_cost' => 0,
            'shipping_courier' => 'Self Pickup',
        ]);
        
        $response->assertRedirect();
        
        // Verify order created with selected address
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'address_id' => $address2->id,
        ]);
        
        // Verify order NOT created with default address
        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($address2->id, $order->address_id);
    }
}
