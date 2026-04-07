<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class ProductRepository implements ProductRepositoryInterface
{
    public function find(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Cache::remember("product:{$slug}", 1800, function () use ($slug) {
            return Product::where('slug', $slug)
                ->active()
                ->with('category')
                ->first();
        });
    }

    public function getActive(array $filters = []): Collection
    {
        $query = Product::active()->inStock()->with('category');
        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function paginateActive(int $perPage = 12, array $filters = []): LengthAwarePaginator
    {
        $query = Product::active()->inStock()->with('category');
        $this->applyFilters($query, $filters);

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);
        $this->clearProductCache($product);

        return $product->load('category');
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        $this->clearProductCache($product);

        return $product->fresh()->load('category');
    }

    public function delete(Product $product): bool
    {
        $this->clearProductCache($product);

        return $product->delete();
    }

    public function getByCategory(int $categoryId, array $filters = []): Collection
    {
        $query = Product::active()
            ->inStock()
            ->where('category_id', $categoryId)
            ->with('category');

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function search(string $query, array $filters = []): Collection
    {
        $searchQuery = Product::active()
            ->inStock()
            ->with('category')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            });

        $this->applyFilters($searchQuery, $filters);

        return $searchQuery->get();
    }

    public function getRelated(Product $product, int $limit = 4): Collection
    {
        return Cache::remember("related_products:{$product->category_id}:{$product->id}", 1800, function () use ($product, $limit) {
            return Product::active()
                ->inStock()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit($limit)
                ->get();
        });
    }

    public function getLowStock(int $threshold = 10): Collection
    {
        return Product::active()
            ->where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->with('category')
            ->orderBy('stock', 'asc')
            ->get();
    }

    public function updateStock(Product $product, int $quantity, string $reason): Product
    {
        $product->update(['stock' => $quantity]);
        $this->clearProductCache($product);

        return $product->fresh();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        // Sorting
        if (! empty($filters['sort'])) {
            match ($filters['sort']) {
                'harga-terendah' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
                'harga-tertinggi' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
                'terlaris' => $query->orderBy('view_count', 'desc'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        // Category filter
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Price range
        if (! empty($filters['min_price'])) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [$filters['min_price']]);
        }

        if (! empty($filters['max_price'])) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [$filters['max_price']]);
        }
    }

    /**
     * Clear product cache
     */
    private function clearProductCache(Product $product): void
    {
        Cache::forget("product:{$product->slug}");
        Cache::forget("related_products:{$product->category_id}:{$product->id}");
        Cache::forget('active_categories');
        Cache::forget('category_product_counts');
    }
}
