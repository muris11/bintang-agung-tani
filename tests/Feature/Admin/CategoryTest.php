<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
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

    public function test_admin_can_view_categories_list(): void
    {
        $admin = $this->createAdminUser();
        $categories = Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/kategori');

        $response->assertOk();
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createAdminUser();
        $categoryData = [
            'name' => 'Kategori Test Baru',
            'description' => 'Deskripsi kategori test',
            'icon' => 'ph-plant',
            'sort_order' => 1,
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)->post('/admin/kategori', $categoryData);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('categories', [
            'name' => 'Kategori Test Baru',
        ]);
    }

    public function test_admin_cannot_create_category_without_name(): void
    {
        $admin = $this->createAdminUser();
        $categoryData = [
            'name' => '',
            'description' => 'Deskripsi tanpa nama',
        ];

        $response = $this->actingAs($admin)->post('/admin/kategori', $categoryData);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::factory()->create([
            'name' => 'Nama Lama',
        ]);

        $response = $this->actingAs($admin)->put("/admin/kategori/{$category->id}", [
            'name' => 'Nama Baru',
            'description' => $category->description,
        ]);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/kategori/{$category->id}");

        $response->assertRedirect('/admin/kategori');
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/kategori/{$category->id}");

        $response->assertRedirect('/admin/kategori');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_toggle_category_active_status(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch("/admin/kategori/{$category->id}/toggle");

        $response->assertRedirect();
        $category->refresh();
        $this->assertFalse($category->is_active);
    }

    public function test_admin_can_view_create_category_form(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/kategori/create');

        $response->assertOk();
        $response->assertViewIs('admin.tambah-kategori');
        $response->assertSee('Tambah Kategori Baru');
        $response->assertSee('Nama Kategori');
        $response->assertSee('Ikon Kategori');
    }

    public function test_non_admin_cannot_access_categories(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get('/admin/kategori');

        $response->assertRedirect('/user/dashboard');
    }
}
