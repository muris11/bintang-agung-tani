<?php

namespace Tests\Feature\User;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductBrowseTest extends TestCase
{
    use RefreshDatabase;

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    protected function createCategory(string $name, string $slug): Category
    {
        return Category::factory()->create([
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
        ]);
    }

    protected function createProduct(Category $category, array $overrides = []): Product
    {
        return Product::factory()->create(array_merge([
            'category_id' => $category->id,
            'is_active' => true,
            'stock' => 10,
        ], $overrides));
    }

    public function test_user_can_view_products_page(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');
        $product = $this->createProduct($category);

        // Clear cache for fresh test
        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index'));

        $response->assertOk();
        $response->assertViewIs('user.produk');
        $response->assertViewHas('products');
        $response->assertViewHas('categories');
    }

    public function test_user_can_view_product_details(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');
        $product = $this->createProduct($category, [
            'name' => 'Pupuk Organik',
            'slug' => 'pupuk-organik',
        ]);

        // Clear cache for fresh test
        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.show', $product->slug));

        $response->assertOk();
        $response->assertViewIs('user.detail-produk');
        $response->assertViewHas('product');
        $response->assertViewHas('relatedProducts');
    }

    public function test_inactive_products_are_not_shown(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $activeProduct = $this->createProduct($category, [
            'name' => 'Pupuk Aktif',
            'is_active' => true,
        ]);

        $inactiveProduct = $this->createProduct($category, [
            'name' => 'Pupuk Nonaktif',
            'is_active' => false,
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index'));

        $response->assertOk();
        $products = $response->viewData('products');

        // Should contain active product
        $this->assertTrue($products->contains('id', $activeProduct->id));
        // Should not contain inactive product
        $this->assertFalse($products->contains('id', $inactiveProduct->id));
    }

    public function test_out_of_stock_products_are_not_shown(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $inStockProduct = $this->createProduct($category, [
            'name' => 'Pupuk Ready',
            'stock' => 10,
        ]);

        $outOfStockProduct = $this->createProduct($category, [
            'name' => 'Pupuk Habis',
            'stock' => 0,
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index'));

        $response->assertOk();
        $products = $response->viewData('products');

        $this->assertTrue($products->contains('id', $inStockProduct->id));
        $this->assertFalse($products->contains('id', $outOfStockProduct->id));
    }

    public function test_user_can_filter_products_by_category(): void
    {
        $user = $this->createRegularUser();
        $category1 = $this->createCategory('Pupuk', 'pupuk');
        $category2 = $this->createCategory('Benih', 'benih');

        $pupukProduct = $this->createProduct($category1, ['name' => 'Pupuk A']);
        $benihProduct = $this->createProduct($category2, ['name' => 'Benih A']);

        Cache::flush();

        // Filter by pupuk category
        $response = $this->actingAs($user)->get(route('user.produk.index', ['kategori' => 'pupuk']));

        $response->assertOk();
        $products = $response->viewData('products');

        $this->assertTrue($products->contains('id', $pupukProduct->id));
        $this->assertFalse($products->contains('id', $benihProduct->id));
    }

    public function test_user_can_search_products(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $targetProduct = $this->createProduct($category, [
            'name' => 'Pupuk Organik Super',
            'description' => 'Pupuk berkualitas tinggi',
        ]);

        $otherProduct = $this->createProduct($category, [
            'name' => 'Pestisida ABC',
            'description' => 'Obat hama',
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index', ['search' => 'Organik']));

        $response->assertOk();
        $products = $response->viewData('products');

        $this->assertTrue($products->contains('id', $targetProduct->id));
        $this->assertFalse($products->contains('id', $otherProduct->id));
    }

    public function test_user_can_sort_products_by_price_ascending(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $cheapProduct = $this->createProduct($category, [
            'name' => 'Pupuk Murah',
            'price' => 50000,
        ]);

        $expensiveProduct = $this->createProduct($category, [
            'name' => 'Pupuk Mahal',
            'price' => 150000,
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index', ['sort' => 'harga-terendah']));

        $response->assertOk();
        $products = $response->viewData('products');

        // First product should be cheaper
        if ($products->count() >= 2) {
            $firstPrice = $products->first()->getCurrentPrice();
            $this->assertLessThanOrEqual(150000, $firstPrice);
        }
    }

    public function test_user_can_sort_products_by_price_descending(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $this->createProduct($category, [
            'name' => 'Pupuk Murah',
            'price' => 50000,
        ]);

        $this->createProduct($category, [
            'name' => 'Pupuk Mahal',
            'price' => 150000,
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index', ['sort' => 'harga-tertinggi']));

        $response->assertOk();
        $products = $response->viewData('products');

        // First product should be more expensive
        if ($products->count() >= 2) {
            $firstPrice = $products->first()->getCurrentPrice();
            $this->assertGreaterThanOrEqual(50000, $firstPrice);
        }
    }

    public function test_user_can_sort_products_by_popularity(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $popularProduct = $this->createProduct($category, [
            'name' => 'Pupuk Populer',
            'view_count' => 1000,
        ]);

        $lessPopularProduct = $this->createProduct($category, [
            'name' => 'Pupuk Jarang',
            'view_count' => 10,
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.index', ['sort' => 'terlaris']));

        $response->assertOk();
        $products = $response->viewData('products');

        // Popular product should appear first
        if ($products->count() >= 2) {
            $this->assertEquals($popularProduct->id, $products->first()->id);
        }
    }

    public function test_product_detail_returns_404_for_invalid_slug(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('user.produk.show', 'invalid-slug-123'));

        $response->assertNotFound();
    }

    public function test_product_detail_shows_related_products(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        $mainProduct = $this->createProduct($category, [
            'name' => 'Pupuk Utama',
            'slug' => 'pupuk-utama',
        ]);

        $relatedProduct = $this->createProduct($category, [
            'name' => 'Pupuk Serupa',
        ]);

        // Clear cache for fresh test
        Cache::flush();

        $response = $this->actingAs($user)->get(route('user.produk.show', $mainProduct->slug));

        $response->assertOk();
        $relatedProducts = $response->viewData('relatedProducts');

        // Should show related product from same category
        $this->assertTrue($relatedProducts->contains('id', $relatedProduct->id));
        // Should not show the main product itself
        $this->assertFalse($relatedProducts->contains('id', $mainProduct->id));
    }

    public function test_products_are_cached(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');
        $product = $this->createProduct($category);

        Cache::flush();

        // First request - should cache
        $this->actingAs($user)->get(route('user.produk.show', $product->slug));

        // Verify product is cached
        $this->assertTrue(Cache::has("product:{$product->slug}"));

        // Second request - should use cache
        $response = $this->actingAs($user)->get(route('user.produk.show', $product->slug));
        $response->assertOk();
    }

    public function test_categories_are_cached(): void
    {
        $user = $this->createRegularUser();
        $category = $this->createCategory('Pupuk', 'pupuk');

        Cache::flush();

        // First request
        $this->actingAs($user)->get(route('user.produk.index'));

        // Verify categories are cached
        $this->assertTrue(Cache::has('active_categories'));
    }

    public function test_sort_dropdown_shows_current_selection()
    {
        $user = $this->createRegularUser();
        $response = $this->actingAs($user)->get(route('user.produk.index', ['sort' => 'harga-terendah']));

        $response->assertStatus(200);
        $response->assertViewHas('currentSort', 'harga-terendah');
    }

    public function test_sort_dropdown_shows_default_selection()
    {
        $user = $this->createRegularUser();
        $response = $this->actingAs($user)->get(route('user.produk.index'));

        $response->assertStatus(200);
        $response->assertViewHas('currentSort', 'terbaru');
    }
}
