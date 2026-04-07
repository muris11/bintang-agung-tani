<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateProductAction
{
    /**
     * Create a new product with auto-generated slug and SKU
     */
    public function handle(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Auto-generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
            }

            // Auto-generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateUniqueSku();
            }

            $product = Product::create($data);

            return $product->load('category');
        });
    }

    /**
     * Generate unique slug from product name
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate unique SKU
     */
    private function generateUniqueSku(): string
    {
        do {
            $sku = strtoupper(Str::random(8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
