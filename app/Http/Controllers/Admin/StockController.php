<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display stock list with filtering
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by product name/sku
        if ($request->has('product') && $request->product) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->product}%")
                    ->orWhere('sku', 'like', "%{$request->product}%");
            });
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 10);
                    break;
                case 'low_stock':
                    $query->where('stock', '<=', 10)->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        $products = $query->with('category')
            ->orderBy('stock', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Get stock stats
        $stats = $this->stockService->getStockStats();

        return view('admin.stok', compact('products', 'stats'));
    }

    /**
     * Show stock details for a product
     */
    public function show(Product $product)
    {
        $stockLogs = $this->stockService->getStockHistory($product, 50);

        return view('admin.stok-detail', compact('product', 'stockLogs'));
    }

    /**
     * Show form to edit stock for a product
     */
    public function edit(Product $product)
    {
        $stockLogs = $this->stockService->getStockHistory($product, 10);

        return view('admin.edit-stok', compact('product', 'stockLogs'));
    }

    /**
     * Update product stock
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $newStock = $validated['stock'];
        $currentStock = $product->stock;
        $difference = $newStock - $currentStock;

        if ($difference > 0) {
            // Stock increase
            $product->increaseStock($difference, $validated['reason']);
        } elseif ($difference < 0) {
            // Stock decrease
            $decreaseAmount = abs($difference);
            $product->decreaseStock($decreaseAmount, $validated['reason']);
        }

        return redirect()->back()->with('success', 'Stock updated successfully');
    }

    /**
     * Display stock logs with filtering
     */
    public function logs(Request $request)
    {
        $query = \App\Models\StockLog::query()
            ->with(['product', 'order', 'createdBy']);

        // Filter by product
        if ($request->has('product') && $request->product) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->product}%")
                    ->orWhere('sku', 'like', "%{$request->product}%");
            });
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by type
        if ($request->has('type') && in_array($request->type, ['increase', 'decrease'])) {
            $query->where('type', $request->type);
        }

        $stockLogs = $query->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        return view('admin.stock-logs', compact('stockLogs'));
    }
}
