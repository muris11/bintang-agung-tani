<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_view_stock_list(): void
    {
        Category::factory()->create();
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/stok');

        $response->assertOk();
        $response->assertViewIs('admin.stok');
        $response->assertViewHas('products');
        $response->assertViewHas('stats');
    }

    public function test_admin_can_filter_by_stock_status(): void
    {
        Category::factory()->create();
        Product::factory()->create(['stock' => 20, 'name' => 'In Stock']);
        Product::factory()->create(['stock' => 5, 'name' => 'Low Stock']);
        Product::factory()->create(['stock' => 0, 'name' => 'Out of Stock']);

        // Test in_stock filter
        $response = $this->actingAs($this->admin)
            ->get('/admin/stok?stock_status=in_stock');
        $response->assertOk();
        $products = $response->viewData('products');
        $this->assertTrue($products->contains('name', 'In Stock'));
        $this->assertFalse($products->contains('name', 'Low Stock'));
        $this->assertFalse($products->contains('name', 'Out of Stock'));

        // Test low_stock filter
        $response = $this->actingAs($this->admin)
            ->get('/admin/stok?stock_status=low_stock');
        $response->assertOk();
        $products = $response->viewData('products');
        $this->assertFalse($products->contains('name', 'In Stock'));
        $this->assertTrue($products->contains('name', 'Low Stock'));
        $this->assertFalse($products->contains('name', 'Out of Stock'));

        // Test out_of_stock filter
        $response = $this->actingAs($this->admin)
            ->get('/admin/stok?stock_status=out_of_stock');
        $response->assertOk();
        $products = $response->viewData('products');
        $this->assertFalse($products->contains('name', 'In Stock'));
        $this->assertFalse($products->contains('name', 'Low Stock'));
        $this->assertTrue($products->contains('name', 'Out of Stock'));
    }

    public function test_admin_can_update_product_stock(): void
    {
        Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/stok/{$product->id}", [
                'stock' => 25,
                'reason' => 'Manual stock adjustment',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals(25, $product->stock);
    }

    public function test_admin_can_view_stock_history(): void
    {
        Category::factory()->create();
        $product = Product::factory()->create(['stock' => 100]);

        // Create some stock logs
        StockLog::factory()->count(3)->create([
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/stok/{$product->id}");

        $response->assertOk();
        $response->assertViewIs('admin.stok-detail');
        $response->assertViewHas('product');
        $response->assertViewHas('stockLogs');
    }

    public function test_stock_update_creates_log_entry(): void
    {
        Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($this->admin)
            ->patch("/admin/stok/{$product->id}", [
                'stock' => 25,
                'reason' => 'Restocking for sale',
            ]);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'type' => 'increase',
            'quantity' => 15,
            'before_stock' => 10,
            'after_stock' => 25,
            'reason' => 'Restocking for sale',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_stock_logs_page(): void
    {
        Category::factory()->create();
        $product = Product::factory()->create(['stock' => 100]);

        // Create some stock logs
        StockLog::factory()->count(5)->create([
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stock.logs'));

        $response->assertOk();
        $response->assertViewIs('admin.stock-logs');
        $response->assertViewHas('stockLogs');
    }
}
