<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
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

    public function test_admin_can_view_settings(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertViewIs('admin.settings.index');
        $response->assertViewHas('uiSettings');
        $response->assertViewHas('storeSettings');
        $response->assertViewHas('contactSettings');
        $response->assertViewHas('operationalSettings');
        $response->assertViewHas('socialSettings');
        $response->assertViewHas('statisticsSettings');
        $response->assertViewHas('categories');
    }

    public function test_settings_page_shows_system_info(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Aplikasi');
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('admin.settings.index'));

        $response->assertRedirect('/user/dashboard');
    }

    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get(route('admin.settings.index'));

        $response->assertRedirect('/login');
    }

    public function test_admin_can_reset_settings(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post(route('admin.settings.reset'));

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = $this->createAdminUser();

        $settingsData = [
            'show_welcome_banner' => true,
            'show_categories_grid' => true,
            'products_per_page' => 24,
            'enable_cart_drawer' => true,
            'sidebar_category_count' => 10,
        ];

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.update'), $settingsData);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        // Verify settings were saved to database
        $this->assertDatabaseHas('settings', [
            'key' => 'show_welcome_banner',
            'value' => '1',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'products_per_page',
            'value' => '24',
        ]);
    }

    public function test_admin_can_update_boolean_settings(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'show_welcome_banner' => false,
                'show_categories_grid' => false,
                'enable_cart_drawer' => false,
            ]);

        $response->assertRedirect();

        // Verify boolean false values are saved as "0"
        $this->assertDatabaseHas('settings', [
            'key' => 'show_welcome_banner',
            'value' => '0',
        ]);
    }

    public function test_products_per_page_has_validation(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'products_per_page' => 5, // Below minimum of 8
            ]);

        $response->assertSessionHasErrors('products_per_page');
    }

    public function test_sidebar_categories_must_be_valid_category_ids(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'sidebar_categories' => [999999, 888888], // Invalid category IDs
            ]);

        $response->assertSessionHasErrors('sidebar_categories.*');
    }

    public function test_reset_settings_removes_custom_values(): void
    {
        $admin = $this->createAdminUser();

        // Create some custom settings
        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'products_per_page' => 50,
                'show_welcome_banner' => false,
            ]);

        // Reset settings
        $response = $this->actingAs($admin)
            ->post(route('admin.settings.reset'));

        $response->assertRedirect();

        // Check that UI settings were removed
        $this->assertDatabaseMissing('settings', [
            'group' => 'ui',
            'key' => 'products_per_page',
        ]);
    }
}
