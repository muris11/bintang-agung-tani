<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Find product by ID
     */
    public function find(int $id): ?Product;

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?Product;

    /**
     * Get all active products
     */
    public function getActive(array $filters = []): Collection;

    /**
     * Get paginated active products
     */
    public function paginateActive(int $perPage = 12, array $filters = []): LengthAwarePaginator;

    /**
     * Create new product
     */
    public function create(array $data): Product;

    /**
     * Update product
     */
    public function update(Product $product, array $data): Product;

    /**
     * Delete product
     */
    public function delete(Product $product): bool;

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId, array $filters = []): Collection;

    /**
     * Search products
     */
    public function search(string $query, array $filters = []): Collection;

    /**
     * Get related products
     */
    public function getRelated(Product $product, int $limit = 4): Collection;

    /**
     * Get low stock products
     */
    public function getLowStock(int $threshold = 10): Collection;

    /**
     * Update stock
     */
    public function updateStock(Product $product, int $quantity, string $reason): Product;
}
