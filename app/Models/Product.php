<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_price',
        'stock',
        'min_order',
        'max_order',
        'sku',
        'unit',
        'weight',
        'images',
        'featured_image',
        'is_featured',
        'is_active',
        'view_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'min_order' => 'integer',
        'max_order' => 'integer',
        'view_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'images' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });

        // Clear cache on model changes
        static::saved(function ($product) {
            app(\App\Services\ProductCacheService::class)->clearForProduct($product);
        });

        static::deleted(function ($product) {
            app(\App\Services\ProductCacheService::class)->clearForProduct($product);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCurrentPrice(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function getOriginalPrice(): float
    {
        return $this->price;
    }

    public function hasDiscount(): bool
    {
        return ! is_null($this->discount_price) && $this->discount_price < $this->price;
    }

    public function getDiscountPercentage(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function isAvailableForOrder(int $quantity): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $this->hasStock($quantity)) {
            return false;
        }

        if (! is_null($this->min_order) && $quantity < $this->min_order) {
            return false;
        }

        if (! is_null($this->max_order) && $quantity > $this->max_order) {
            return false;
        }

        return true;
    }

    public function getAvailabilityMessage(int $quantity): string
    {
        if (! $this->is_active) {
            return 'Product is not available';
        }

        if (! $this->hasStock($quantity)) {
            return "Only {$this->stock} item(s) available in stock";
        }

        if (! is_null($this->min_order) && $quantity < $this->min_order) {
            return "Minimum order is {$this->min_order} item(s)";
        }

        if (! is_null($this->max_order) && $quantity > $this->max_order) {
            return "Maximum order is {$this->max_order} item(s)";
        }

        return 'Available';
    }

    public function getImages(): array
    {
        if (! empty($this->images) && is_array($this->images)) {
            return $this->images;
        }

        return [];
    }

    public function getFirstImage(): ?string
    {
        if (! empty($this->featured_image)) {
            return $this->featured_image;
        }

        if (! empty($this->images) && is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }

        return null;
    }

    public function getFormattedPrice(): string
    {
        return 'Rp '.number_format($this->getCurrentPrice(), 0, ',', '.');
    }

    public function getFormattedOriginalPrice(): string
    {
        return 'Rp '.number_format($this->getOriginalPrice(), 0, ',', '.');
    }

    public function decreaseStock(int $quantity, string $reason, ?int $orderId = null): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        // Use atomic database update to prevent race conditions
        $affected = DB::update(
            'UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?',
            [$quantity, $this->id, $quantity]
        );

        if ($affected === 0) {
            // Either product not found or insufficient stock
            return false;
        }

        // Refresh model to get updated stock value
        $beforeStock = $this->stock;
        $this->refresh();
        $afterStock = $this->stock;

        // Log stock movement
        $stockService = app(\App\Services\StockService::class);
        $stockService->recordStockChangeWithValues(
            $this,
            $quantity,
            'decrease',
            $reason,
            $beforeStock,
            $afterStock,
            $orderId
        );

        return true;
    }

    public function increaseStock(int $quantity, string $reason, ?int $orderId = null): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $beforeStock = $this->stock;
        $this->stock += $quantity;
        $afterStock = $this->stock;
        $this->save();

        // Log stock movement
        $stockService = app(\App\Services\StockService::class);
        $stockService->recordStockChangeWithValues(
            $this,
            $quantity,
            'increase',
            $reason,
            $beforeStock,
            $afterStock,
            $orderId
        );

        return true;
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $id)
    {
        return $query->where('category_id', $id);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('sku', 'like', "%{$keyword}%");
        });
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->where(function ($q) use ($min, $max) {
            $q->where('price', '>=', $min)
                ->where('price', '<=', $max);
        });
    }
}
