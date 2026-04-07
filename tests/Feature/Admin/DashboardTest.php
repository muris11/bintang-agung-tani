<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_view_dashboard(): void
    {
        $admin = $this->createAdminUser();

        // Create some test data
        Category::factory()->count(3)->create();
        Product::factory()->count(5)->create();
        Order::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('totalProducts');
        $response->assertViewHas('totalCategories');
        $response->assertViewHas('recentOrders');
        $response->assertViewHas('lowStockProducts');
    }

    public function test_dashboard_shows_correct_statistics(): void
    {
        $admin = $this->createAdminUser();

        Category::factory()->count(5)->create();
        Product::factory()->count(10)->create();
        Order::factory()->count(3)->create(['total_amount' => 100000]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();

        // Verify that stats variables exist and have correct types
        $totalProducts = $response->viewData('totalProducts');
        $totalCategories = $response->viewData('totalCategories');
        $ordersThisMonth = $response->viewData('ordersThisMonth');

        $this->assertIsInt($totalProducts);
        $this->assertIsInt($totalCategories);
        $this->assertIsInt($ordersThisMonth);

        // Verify counts are at least what we created
        $this->assertGreaterThanOrEqual(10, $totalProducts);
        $this->assertGreaterThanOrEqual(5, $totalCategories);
        $this->assertGreaterThanOrEqual(3, $ordersThisMonth);
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect('/user/dashboard');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect('/login');
    }

    public function test_dashboard_shows_low_stock_products(): void
    {
        $admin = $this->createAdminUser();

        Category::factory()->create();
        $lowStockProduct = Product::factory()->create(['stock' => 3, 'is_active' => true]);
        $normalStockProduct = Product::factory()->create(['stock' => 50, 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $lowStockProducts = $response->viewData('lowStockProducts');

        $this->assertTrue($lowStockProducts->contains('id', $lowStockProduct->id));
        $this->assertFalse($lowStockProducts->contains('id', $normalStockProduct->id));
    }
}
