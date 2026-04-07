<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * The stock service instance.
     */
    protected StockService $stockService;

    /**
     * Create a new controller instance.
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '=', 0);
                    break;
                case 'low_stock':
                    $query->where('stock', '>', 0)->where('stock', '<', 10);
                    break;
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->ordered()->get();

        return view('admin.kelola-produk', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();

        return view('admin.tambah-produk', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());

        return redirect('/admin/produk')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category']);

        // Load stock logs
        $stockLogs = $product->stockLogs()->with(['order', 'createdBy'])->latest()->limit(10)->get();

        return view('admin.detail-produk', compact('product', 'stockLogs'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();

        return view('admin.edit-produk', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect('/admin/produk')
            ->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        // Check if product has order items (using relationship if exists)
        $hasOrderItems = false;
        if (method_exists($product, 'orderItems')) {
            $hasOrderItems = $product->orderItems()->exists();
        }

        if ($hasOrderItems) {
            // Soft delete if has order history
            $product->delete();
            $message = 'Produk berhasil dihapus (soft delete) karena memiliki riwayat pesanan';
        } else {
            // Force delete if no order history
            $product->forceDelete();
            $message = 'Produk berhasil dihapus secara permanen';
        }

        return redirect('/admin/produk')
            ->with('success', $message);
    }

    /**
     * Toggle the active status of the product.
     */
    public function toggleActive(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()
            ->with('success', "Produk berhasil {$status}");
    }

    /**
     * Toggle the featured status of the product.
     */
    public function toggleFeatured(Product $product)
    {
        $product->update([
            'is_featured' => ! $product->is_featured,
        ]);

        $status = $product->is_featured ? 'ditandai sebagai unggulan' : 'dihapus dari unggulan';

        return back()
            ->with('success', "Produk berhasil {$status}");
    }

    /**
     * Update the stock of the specified product.
     */
    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $newStock = $validated['stock'];
        $currentStock = $product->stock;
        $reason = $validated['reason'];

        if ($newStock > $currentStock) {
            // Increase stock
            $quantity = $newStock - $currentStock;
            $product->increaseStock($quantity, $reason);
        } elseif ($newStock < $currentStock) {
            // Decrease stock
            $quantity = $currentStock - $newStock;
            $product->decreaseStock($quantity, $reason);
        }

        return back()
            ->with('success', 'Stok produk berhasil diperbarui');
    }
}
