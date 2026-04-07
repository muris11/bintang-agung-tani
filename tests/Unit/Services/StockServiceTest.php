<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = new StockService;
    }

    public function test_stock_change_is_recorded(): void
    {
        $product = Product::factory()->create(['stock' => 100]);
        $user = User::factory()->create();

        $this->actingAs($user);

        $stockLog = $this->stockService->recordStockChange(
            $product,
            10,
            'decrease',
            'Test order',
            null,
            $user->id
        );

        $this->assertInstanceOf(StockLog::class, $stockLog);
        $this->assertEquals($product->id, $stockLog->product_id);
        $this->assertEquals('decrease', $stockLog->type);
        $this->assertEquals(10, $stockLog->quantity);
        $this->assertEquals(100, $stockLog->before_stock);
        $this->assertEquals(90, $stockLog->after_stock);
        $this->assertEquals('Test order', $stockLog->reason);
        $this->assertEquals($user->id, $stockLog->created_by);
    }

    public function test_stock_history_can_be_retrieved(): void
    {
        $product = Product::factory()->create(['stock' => 100]);
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create some stock logs
        StockLog::factory()->count(5)->create([
            'product_id' => $product->id,
        ]);

        $history = $this->stockService->getStockHistory($product, 10);

        $this->assertCount(5, $history);
        $this->assertInstanceOf(StockLog::class, $history->first());
    }

    public function test_low_stock_products_can_be_identified(): void
    {
        // Create products with different stock levels
        Product::factory()->create(['stock' => 5, 'name' => 'Low Stock 1']);
        Product::factory()->create(['stock' => 8, 'name' => 'Low Stock 2']);
        Product::factory()->create(['stock' => 15, 'name' => 'Normal Stock']);
        Product::factory()->create(['stock' => 0, 'name' => 'Out of Stock']);

        $lowStockProducts = $this->stockService->getLowStockProducts(10);

        $this->assertCount(2, $lowStockProducts);
        $this->assertTrue($lowStockProducts->contains('name', 'Low Stock 1'));
        $this->assertTrue($lowStockProducts->contains('name', 'Low Stock 2'));
        $this->assertFalse($lowStockProducts->contains('name', 'Normal Stock'));
        $this->assertFalse($lowStockProducts->contains('name', 'Out of Stock'));
    }

    public function test_stock_stats_are_calculated(): void
    {
        // Create products with various stock levels
        Product::factory()->create(['stock' => 20, 'price' => 100]); // Normal
        Product::factory()->create(['stock' => 5, 'price' => 200]);    // Low
        Product::factory()->create(['stock' => 3, 'price' => 150]);    // Low
        Product::factory()->create(['stock' => 0, 'price' => 100]);    // Out of stock
        Product::factory()->create(['stock' => 0, 'price' => 50]);     // Out of stock

        $stats = $this->stockService->getStockStats();

        $this->assertEquals(5, $stats['total_products']);
        $this->assertEquals(2, $stats['low_stock_count']);
        $this->assertEquals(2, $stats['out_of_stock_count']);
        $this->assertEquals(3450.0, $stats['total_inventory_value']);
    }
}
