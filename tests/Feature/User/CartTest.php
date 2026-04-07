<?php

namespace Tests\Feature\User;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    protected function createProduct(int $stock = 10): Product
    {
        $category = Category::factory()->create();

        return Product::factory()->create([
            'category_id' => $category->id,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        $response = $this->actingAs($user)->post('/user/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_user_can_update_cart_quantity(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->getCurrentPrice(),
            'subtotal' => $product->getCurrentPrice(),
        ]);

        $response = $this->actingAs($user)->patch("/user/cart/items/{$cartItem->id}", [
            'quantity' => 3,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    public function test_user_cannot_add_more_than_stock(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct(stock: 5);

        $response = $this->actingAs($user)->post('/user/cart/add', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->getCurrentPrice(),
            'subtotal' => $product->getCurrentPrice() * 2,
        ]);

        $response = $this->actingAs($user)->delete("/user/cart/items/{$cartItem->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_cart_calculates_total_correctly(): void
    {
        $user = $this->createRegularUser();
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'unit_price' => $product1->getCurrentPrice(),
            'subtotal' => $product1->getCurrentPrice() * 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'unit_price' => $product2->getCurrentPrice(),
            'subtotal' => $product2->getCurrentPrice() * 3,
        ]);

        $cart->recalculateTotals();

        $expectedTotal = ($product1->getCurrentPrice() * 2) + ($product2->getCurrentPrice() * 3);
        $expectedItems = 5;

        $this->assertEquals($expectedTotal, $cart->fresh()->total_amount);
        $this->assertEquals($expectedItems, $cart->fresh()->total_items);
    }

    public function test_cart_is_cleared_after_order(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->getCurrentPrice(),
            'subtotal' => $product->getCurrentPrice() * 2,
        ]);

        $response = $this->actingAs($user)->delete('/user/cart/clear');

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);

        $cart->refresh();
        $this->assertEquals(0, $cart->total_amount);
        $this->assertEquals(0, $cart->total_items);
    }

    public function test_cart_quantity_can_be_updated_via_form(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Add product to cart
        $this->actingAs($user)->post('/user/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $cartItem = CartItem::whereHas('cart', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first();

        $this->assertNotNull($cartItem, 'Cart item should exist');

        // Test increase quantity
        $response = $this->actingAs($user)->patch(
            "/user/cart/items/{$cartItem->id}",
            ['quantity' => 3]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    public function test_user_gets_existing_cart_or_create_new(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        $cart = Cart::getOrCreateForUser($user->id);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'id' => $cart->id,
        ]);

        $this->actingAs($user)->post('/user/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $cart2 = Cart::getOrCreateForUser($user->id);
        $this->assertEquals($cart->id, $cart2->id);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_cart_quantity_buttons_update_database(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 50000, 'is_active' => true]);
        
        // Add to cart
        $this->actingAs($user)->post(route('user.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        
        $cartItem = CartItem::whereHas('cart', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first();
        
        $this->assertNotNull($cartItem);
        
        // Store the unit_price for subtotal calculation
        $unitPrice = $cartItem->unit_price;
        
        // Test increment via form submission (simulating button click)
        $response = $this->actingAs($user)->patch(
            route('user.cart.update', $cartItem),
            ['quantity' => 3]
        );
        
        $response->assertRedirect();
        
        // Verify quantity updated
        $cartItem->refresh();
        $this->assertEquals(3, $cartItem->quantity);
        
        // Verify subtotal recalculated (3 * unit_price)
        $this->assertEquals(3 * $unitPrice, $cartItem->subtotal);
    }

    public function test_cart_item_can_be_deleted_with_feedback(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct();

        // Add to cart
        $this->actingAs($user)->post('/user/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $cartItem = CartItem::whereHas('cart', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first();

        $this->assertNotNull($cartItem, 'Cart item should exist');

        // Delete item
        $response = $this->actingAs($user)->delete("/user/cart/items/{$cartItem->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }
}
