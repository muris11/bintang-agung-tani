<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Update product stock.
     */
    public function updateStock(Product $product, int $newStock, string $reason): bool
    {
        $currentStock = $product->stock;

        if ($newStock > $currentStock) {
            $quantity = $newStock - $currentStock;

            return $product->increaseStock($quantity, $reason);
        } elseif ($newStock < $currentStock) {
            $quantity = $currentStock - $newStock;

            return $product->decreaseStock($quantity, $reason);
        }

        return true;
    }

    /**
     * Log stock movement.
     */
    public function logStockMovement(Product $product, int $quantity, string $type, string $reason): void
    {
        $this->recordStockChange($product, $quantity, $type, $reason);
    }

    /**
     * Record a stock change in the stock log
     */
    public function recordStockChange(
        Product $product,
        int $quantity,
        string $type,
        string $reason,
        ?int $orderId = null,
        ?int $userId = null
    ): StockLog {
        $beforeStock = $product->stock;

        if ($type === 'decrease') {
            $afterStock = $beforeStock - $quantity;
        } else {
            $afterStock = $beforeStock + $quantity;
        }

        return StockLog::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reason' => $reason,
            'order_id' => $orderId,
            'created_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Record a stock change with explicit before/after values
     */
    public function recordStockChangeWithValues(
        Product $product,
        int $quantity,
        string $type,
        string $reason,
        int $beforeStock,
        int $afterStock,
        ?int $orderId = null,
        ?int $userId = null
    ): StockLog {
        return StockLog::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reason' => $reason,
            'order_id' => $orderId,
            'created_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Get stock history for a product
     */
    public function getStockHistory(Product $product, int $limit = 50): Collection
    {
        return StockLog::forProduct($product->id)
            ->recent($limit)
            ->with(['order', 'createdBy'])
            ->get();
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return Product::where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->get();
    }

    /**
     * Get stock statistics
     */
    public function getStockStats(): array
    {
        $totalProducts = Product::count();

        $lowStockCount = Product::where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->count();

        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        $totalInventoryValue = Product::where('stock', '>', 0)
            ->select(DB::raw('SUM(stock * price) as total_value'))
            ->first()
            ->total_value ?? 0;

        return [
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_inventory_value' => (float) $totalInventoryValue,
        ];
    }
}
