<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Category data with agricultural relevance.
     */
    private array $categories = [
        [
            'name' => 'Pupuk',
            'icon' => 'ph-plant',
            'description' => 'Berbagai jenis pupuk untuk pertanian',
            'subcategories' => ['Pupuk NPK', 'Pupuk Organik', 'Pupuk Urea', 'Pupuk ZA', 'Pupuk SP-36'],
        ],
        [
            'name' => 'Bibit',
            'icon' => 'ph-seedling',
            'description' => 'Bibit tanaman berkualitas unggul',
            'subcategories' => ['Bibit Padi', 'Bibit Jagung', 'Bibit Sayuran', 'Bibit Buah'],
        ],
        [
            'name' => 'Pestisida',
            'icon' => 'ph-bug',
            'description' => 'Pestisida, herbisida, dan fungisida',
            'subcategories' => ['Insektisida', 'Herbisida', 'Fungisida', 'Bakterisida'],
        ],
        [
            'name' => 'Alat Pertanian',
            'icon' => 'ph-tree',
            'description' => 'Peralatan dan alat pertanian',
            'subcategories' => ['Cangkul', 'Sabit', 'Sprayer', 'Sekop', 'Parang'],
        ],
        [
            'name' => 'Benih',
            'icon' => 'ph-grains',
            'description' => 'Benih padi, jagung, dan sayuran',
            'subcategories' => ['Benih Padi', 'Benih Jagung', 'Benih Cabai', 'Benih Tomat'],
        ],
        [
            'name' => 'Media Tanam',
            'icon' => 'ph-drop',
            'description' => 'Media tanam dan cocopeat',
            'subcategories' => ['Cocopeat', 'Sekam Bakar', 'Kompos', 'Pupuk Kandang'],
        ],
        [
            'name' => 'Nutrisi & ZPT',
            'icon' => 'ph-cube',
            'description' => 'Nutrisi, vitamin, dan zat pengatur tumbuh',
            'subcategories' => ['Nutrisi AB Mix', 'ZPT', 'Micronutrient', 'Gandasil'],
        ],
        [
            'name' => 'Obat Hewan',
            'icon' => 'ph-first-aid',
            'description' => 'Obat dan vitamin untuk hewan ternak',
            'subcategories' => ['Vitamin Ternak', 'Obat Cacing', 'Antibiotik', 'Vaksin'],
        ],
        [
            'name' => 'Peralatan Kebun',
            'icon' => 'ph-watering-can',
            'description' => 'Peralatan perawatan kebun dan tanaman',
            'subcategories' => ['Selang', 'Sprinkler', 'Pot Tanaman', 'Tray Semai'],
        ],
        [
            'name' => 'Paket Tanam',
            'icon' => 'ph-package',
            'description' => 'Paket lengkap untuk menanam',
            'subcategories' => ['Paket Hidroponik', 'Paket Kebun', 'Paket Taman'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating categories...');

        foreach ($this->categories as $index => $data) {
            $category = Category::firstOrCreate(
                ['slug' => str()->slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );

            $this->command->info("  ✓ Created: {$category->name}");
        }

        $this->command->info("Created " . count($this->categories) . " categories successfully!");
    }
}
