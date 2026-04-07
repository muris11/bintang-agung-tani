<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Product templates by category.
     */
    private array $productTemplates = [
        'Pupuk' => [
            ['name' => 'NPK Phonska', 'price' => 85000, 'unit' => 'sak'],
            ['name' => 'Urea Premium', 'price' => 65000, 'unit' => 'sak'],
            ['name' => 'ZA Plus', 'price' => 55000, 'unit' => 'sak'],
            ['name' => 'SP-36', 'price' => 75000, 'unit' => 'sak'],
            ['name' => 'KCl', 'price' => 60000, 'unit' => 'sak'],
            ['name' => 'NPK Mutiara', 'price' => 95000, 'unit' => 'sak'],
        ],
        'Bibit' => [
            ['name' => 'Bibit Padi IR64', 'price' => 12000, 'unit' => 'kg'],
            ['name' => 'Bibit Padi Inpari', 'price' => 15000, 'unit' => 'kg'],
            ['name' => 'Bibit Jagung Manis', 'price' => 25000, 'unit' => 'pack'],
            ['name' => 'Bibit Cabai Rawit', 'price' => 18000, 'unit' => 'pack'],
            ['name' => 'Bibit Tomat', 'price' => 15000, 'unit' => 'pack'],
        ],
        'Pestisida' => [
            ['name' => 'Decis 25 EC', 'price' => 45000, 'unit' => 'botol'],
            ['name' => 'Bassa 500 EC', 'price' => 35000, 'unit' => 'botol'],
            ['name' => 'Dursban 20 EC', 'price' => 55000, 'unit' => 'botol'],
            ['name' => 'Curacron 500 EC', 'price' => 65000, 'unit' => 'botol'],
            ['name' => 'Antracol 70 WP', 'price' => 40000, 'unit' => 'pack'],
        ],
        'Alat Pertanian' => [
            ['name' => 'Cangkul Cap Buaya', 'price' => 75000, 'unit' => 'pcs'],
            ['name' => 'Sabit Besi', 'price' => 65000, 'unit' => 'pcs'],
            ['name' => 'Hand Sprayer 16L', 'price' => 185000, 'unit' => 'pcs'],
            ['name' => 'Sekop Tanah', 'price' => 55000, 'unit' => 'pcs'],
            ['name' => 'Parang Duku', 'price' => 85000, 'unit' => 'pcs'],
        ],
        'Benih' => [
            ['name' => 'Benih Padi IR64 Premium', 'price' => 18000, 'unit' => 'kg'],
            ['name' => 'Benih Jagung Hibrida', 'price' => 35000, 'unit' => 'kg'],
            ['name' => 'Benih Cabai Merah', 'price' => 12000, 'unit' => 'pack'],
            ['name' => 'Benih Tomat F1', 'price' => 15000, 'unit' => 'pack'],
        ],
        'Media Tanam' => [
            ['name' => 'Cocopeat 5kg', 'price' => 25000, 'unit' => 'pack'],
            ['name' => 'Sekam Bakar 10kg', 'price' => 15000, 'unit' => 'pack'],
            ['name' => 'Kompos Organik 10kg', 'price' => 20000, 'unit' => 'pack'],
            ['name' => 'Pupuk Kandang 20kg', 'price' => 30000, 'unit' => 'pack'],
        ],
        'Nutrisi & ZPT' => [
            ['name' => 'Nutrisi AB Mix 1L', 'price' => 45000, 'unit' => 'set'],
            ['name' => 'ZPT Rootone F', 'price' => 25000, 'unit' => 'pack'],
            ['name' => 'Micronutrient', 'price' => 35000, 'unit' => 'pack'],
            ['name' => 'Gandasil B', 'price' => 30000, 'unit' => 'pack'],
        ],
        'Obat Hewan' => [
            ['name' => 'Vita-J Ternak', 'price' => 35000, 'unit' => 'botol'],
            ['name' => 'Ivomec', 'price' => 55000, 'unit' => 'botol'],
            ['name' => 'UltraCox', 'price' => 45000, 'unit' => 'pack'],
        ],
        'Peralatan Kebun' => [
            ['name' => 'Selang Air 10m', 'price' => 75000, 'unit' => 'roll'],
            ['name' => 'Sprinkler Set', 'price' => 125000, 'unit' => 'set'],
            ['name' => 'Pot Tanam 25cm', 'price' => 15000, 'unit' => 'pcs'],
            ['name' => 'Tray Semai 104 Lubang', 'price' => 25000, 'unit' => 'pcs'],
        ],
        'Paket Tanam' => [
            ['name' => 'Paket Hidroponik Pemula', 'price' => 285000, 'unit' => 'set'],
            ['name' => 'Paket Kebun Sayur', 'price' => 195000, 'unit' => 'set'],
            ['name' => 'Paket Taman Minimalis', 'price' => 145000, 'unit' => 'set'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating products...');

        $categories = Category::all();
        $totalProducts = 0;

        foreach ($categories as $category) {
            $templates = $this->productTemplates[$category->name] ?? [];

            foreach ($templates as $template) {
                $product = Product::firstOrCreate(
                    ['sku' => 'SKU-' . strtoupper(fake()->lexify('????')) . fake()->unique()->numberBetween(100, 999)],
                    [
                        'category_id' => $category->id,
                        'name' => $template['name'],
                        'slug' => str()->slug($template['name'] . ' ' . fake()->unique()->numberBetween(1, 9999)),
                        'description' => fake()->paragraph(3),
                        'short_description' => fake()->sentence(),
                        'price' => $template['price'],
                        'discount_price' => fake()->boolean(20) ? round($template['price'] * 0.85, -3) : null,
                        'stock' => fake()->numberBetween(20, 150),
                        'min_stock' => 10,
                        'min_order' => 1,
                        'max_order' => fake()->randomElement([null, 50, 100]),
                        'unit' => $template['unit'],
                        'weight' => fake()->randomFloat(2, 0.5, 25),
                        'is_featured' => fake()->boolean(10),
                        'is_active' => true,
                        'view_count' => fake()->numberBetween(0, 500),
                    ]
                );

                $totalProducts++;
                $this->command->info("  ✓ Created: {$product->name}");
            }
        }

        $this->command->info("Created {$totalProducts} products successfully!");
    }
}
