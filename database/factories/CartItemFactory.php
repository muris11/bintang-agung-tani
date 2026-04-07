<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = $product->getCurrentPrice();

        return [
            'cart_id' => Cart::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $quantity,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function forCart(Cart $cart): static
    {
        return $this->state(function (array $attributes) use ($cart) {
            return [
                'cart_id' => $cart->id,
            ];
        });
    }

    public function forProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            $quantity = fake()->numberBetween(1, 5);

            return [
                'product_id' => $product->id,
                'unit_price' => $product->getCurrentPrice(),
                'subtotal' => $product->getCurrentPrice() * $quantity,
            ];
        });
    }
}
