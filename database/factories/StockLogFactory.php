<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockLogFactory extends Factory
{
    protected $model = StockLog::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['increase', 'decrease']);
        $beforeStock = $this->faker->numberBetween(0, 100);
        $quantity = $this->faker->numberBetween(1, 20);

        if ($type === 'decrease' && $beforeStock < $quantity) {
            $quantity = $beforeStock;
        }

        $afterStock = $type === 'increase'
            ? $beforeStock + $quantity
            : $beforeStock - $quantity;

        $reasons = ['Order', 'Restock', 'Adjustment', 'Return', 'Damage', 'Initial Stock'];

        return [
            'product_id' => Product::factory(),
            'type' => $type,
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reason' => $this->faker->randomElement($reasons),
            'order_id' => $this->faker->boolean(30) ? Order::factory() : null,
            'created_by' => $this->faker->boolean(70) ? User::factory() : null,
        ];
    }
}
