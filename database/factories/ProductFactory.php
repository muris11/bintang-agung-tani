<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productPrefixes = ['Pupuk', 'Pestisida', 'Herbisida', 'Fungisida', 'Insektisida', 'Benih', 'Bibit'];
        $productNames = ['NPK Phonska', 'Urea', 'Decis', 'Gramoxone', 'Roundup', 'Padi', 'Jagung', 'Sayuran'];
        $units = ['pcs', 'kg', 'pack', 'liter', 'sak'];
        $prices = [25000, 50000, 75000, 100000, 150000, 200000, 250000, 300000];

        $name = fake()->randomElement($productPrefixes).' '.fake()->randomElement($productNames).' '.fake()->unique()->numberBetween(1, 999);
        $price = fake()->randomElement($prices);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'short_description' => fake()->sentence(),
            'price' => $price,
            'discount_price' => fake()->boolean(30) ? $price * 0.85 : null,
            'stock' => fake()->numberBetween(5, 100),
            'min_order' => 1,
            'max_order' => fake()->randomElement([null, 10, 20, 50]),
            'sku' => 'SKU-'.strtoupper(fake()->lexify('????????')),
            'unit' => fake()->randomElement($units),
            'weight' => fake()->randomFloat(2, 0.1, 50),
            'images' => null,
            'featured_image' => fake()->optional(0.7)->imageUrl(640, 480, 'products'),
            'is_featured' => fake()->boolean(10),
            'is_active' => true,
            'view_count' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product has a discount (20% off).
     */
    public function withDiscount(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'discount_price' => $attributes['price'] * 0.8,
            ];
        });
    }
}
