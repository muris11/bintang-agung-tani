<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = $product->getCurrentPrice();
        $subtotal = $unitPrice * $quantity;

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => 0,
            'subtotal' => $subtotal,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Create an order item with a specific product.
     */
    public function withProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            $quantity = fake()->numberBetween(1, 5);

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => $product->getCurrentPrice(),
                'subtotal' => $product->getCurrentPrice() * $quantity,
            ];
        });
    }
}
