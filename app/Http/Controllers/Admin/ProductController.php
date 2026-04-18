<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
    public function index(Request $request): View
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
    public function create(): View
    {
        $categories = Category::active()->ordered()->get();

        return view('admin.tambah-produk', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('product_images')) {
            $imagePaths = [];

            foreach ($request->file('product_images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = '/storage/'.$path;
            }

            if (! empty($imagePaths)) {
                $validated['featured_image'] = $imagePaths[0];
                $validated['images'] = $imagePaths;
            }
        }

        Product::create($validated);

        return redirect('/admin/produk')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category']);

        // Load stock logs
        $stockLogs = $product->stockLogs()->with(['order', 'createdBy'])->latest()->limit(10)->get();

        return view('admin.detail-produk', compact('product', 'stockLogs'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::active()->ordered()->get();

        return view('admin.edit-produk', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('product_images')) {
            $existingImages = is_array($product->images) ? $product->images : [];
            if ($product->featured_image && ! in_array($product->featured_image, $existingImages, true)) {
                array_unshift($existingImages, $product->featured_image);
            }

            $newImagePaths = [];
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('products', 'public');
                $newImagePaths[] = '/storage/'.$path;
            }

            $mergedImages = array_values(array_slice(array_merge($existingImages, $newImagePaths), 0, 5));
            $validated['images'] = $mergedImages;
            $validated['featured_image'] = $mergedImages[0] ?? null;
        } elseif ($request->hasFile('featured_image_file')) {
            $request->validate([
                'featured_image_file' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            if ($product->featured_image) {
                $oldPath = ltrim(str_replace('/storage/', '', $product->featured_image), '/');
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('featured_image_file')->store('products', 'public');
            $validated['featured_image'] = '/storage/'.$path;
            $validated['images'] = ['/storage/'.$path];
        }

        $product->update($validated);

        return redirect('/admin/produk')
            ->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
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
    public function toggleActive(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

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
    public function toggleFeatured(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

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
    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

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

    /**
     * Update product image.
     */
    public function updateImage(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old featured image if exists
            if ($product->featured_image) {
                $oldPath = str_replace('/storage', '', $product->featured_image);
                Storage::delete($oldPath);
            }

            // Store new image
            $path = $request->file('image')->store('products', 'public');
            $product->update(['featured_image' => '/storage/'.$path]);

            return back()->with('success', 'Gambar produk berhasil diperbarui');
        }

        return back()->with('error', 'Gagal mengupload gambar');
    }

    /**
     * Delete product image.
     */
    public function deleteImage(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        if ($product->featured_image) {
            $path = str_replace('/storage', '', $product->featured_image);
            Storage::delete($path);
            $product->update(['featured_image' => null]);

            return back()->with('success', 'Gambar produk berhasil dihapus');
        }

        return back()->with('error', 'Tidak ada gambar untuk dihapus');
    }
}
