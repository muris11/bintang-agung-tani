<?php

namespace App\DTOs;

final class CreateProductData
{
    public function __construct(
        public readonly int $categoryId,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly ?string $shortDescription,
        public readonly float $price,
        public readonly ?float $discountPrice,
        public readonly int $stock,
        public readonly ?int $minOrder,
        public readonly ?int $maxOrder,
        public readonly ?string $sku,
        public readonly string $unit,
        public readonly ?float $weight,
        public readonly ?array $images,
        public readonly ?string $featuredImage,
        public readonly bool $isFeatured,
        public readonly bool $isActive,
    ) {}

    /**
     * Create from validated request data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            categoryId: (int) $data['category_id'],
            name: $data['name'],
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            shortDescription: $data['short_description'] ?? null,
            price: (float) $data['price'],
            discountPrice: isset($data['discount_price']) ? (float) $data['discount_price'] : null,
            stock: (int) ($data['stock'] ?? 0),
            minOrder: isset($data['min_order']) ? (int) $data['min_order'] : null,
            maxOrder: isset($data['max_order']) ? (int) $data['max_order'] : null,
            sku: $data['sku'] ?? null,
            unit: $data['unit'] ?? 'pcs',
            weight: isset($data['weight']) ? (float) $data['weight'] : null,
            images: $data['images'] ?? null,
            featuredImage: $data['featured_image'] ?? null,
            isFeatured: (bool) ($data['is_featured'] ?? false),
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    /**
     * Convert to array for model creation
     */
    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->shortDescription,
            'price' => $this->price,
            'discount_price' => $this->discountPrice,
            'stock' => $this->stock,
            'min_order' => $this->minOrder,
            'max_order' => $this->maxOrder,
            'sku' => $this->sku,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'images' => $this->images,
            'featured_image' => $this->featuredImage,
            'is_featured' => $this->isFeatured,
            'is_active' => $this->isActive,
        ];
    }
}
