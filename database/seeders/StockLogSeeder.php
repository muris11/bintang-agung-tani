<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockLogSeeder extends Seeder
{
    /**
     * Seed stock logs for all products.
     */
    public function run(): void
    {
        $products = Product::all();
        $admin = User::where('is_admin', true)->first();

        if ($products->isEmpty() || !$admin) {
            $this->command->warn('No products or admin found. Skipping stock logs.');
            return;
        }

        foreach ($products as $product) {
            // Create initial stock entry as 'increase'
            StockLog::create([
                'product_id' => $product->id,
                'type' => 'increase',
                'quantity' => $product->stock,
                'before_stock' => 0,
                'after_stock' => $product->stock,
                'reason' => 'Stok awal produk',
                'created_by' => $admin->id,
            ]);

            // Create some random stock movements (20% chance)
            if (fake()->boolean(20)) {
                $isIncrease = fake()->boolean(50);

                $quantity = fake()->numberBetween(5, 50);
                $stockBefore = $product->stock;

                if ($isIncrease) {
                    $type = 'increase';
                    $stockAfter = $stockBefore + $quantity;
                    $reason = fake()->randomElement([
                        'Restock dari supplier',
                        'Pembelian stok baru',
                        'Retur dari pelanggan',
                    ]);
                } else {
                    $type = 'decrease';
                    $quantity = min($quantity, $stockBefore - 5); // Don't go below safety stock
                    $stockAfter = $stockBefore - $quantity;
                    $reason = fake()->randomElement([
                        'Barang rusak/expired',
                        'Hilang dalam gudang',
                        'Sample untuk display',
                    ]);
                }

                StockLog::create([
                    'product_id' => $product->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'before_stock' => $stockBefore,
                    'after_stock' => $stockAfter,
                    'reason' => $reason,
                    'created_by' => $admin->id,
                ]);

                // Update product stock
                $product->stock = $stockAfter;
                $product->save();
            }
        }

        $this->command->info("Created stock logs for {$products->count()} products");
    }
}
