<?php

namespace Tests\Feature\Admin;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
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

    public function test_admin_can_view_payment_methods_list(): void
    {
        $admin = $this->createAdminUser();
        $methods = PaymentMethod::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/payment-methods');

        $response->assertOk();
        foreach ($methods as $method) {
            $response->assertSee($method->name);
        }
    }

    public function test_admin_can_view_create_payment_method_form(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/payment-methods/create');

        $response->assertOk();
    }

    public function test_admin_can_create_payment_method_with_logo(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();

        $logo = UploadedFile::fake()->image('bca_logo.png');
        $methodData = [
            'name' => 'Transfer BCA',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'PT Bintang Agung',
            'logo' => $logo,
            'instructions' => 'Silakan transfer ke rekening BCA',
            'sort_order' => 1,
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)->post('/admin/payment-methods', $methodData);

        $response->assertRedirect('/admin/payment-methods');
        $this->assertDatabaseHas('payment_methods', [
            'name' => 'Transfer BCA',
            'bank_name' => 'BCA',
        ]);
        Storage::disk('public')->assertExists('payment_methods');
    }

    public function test_admin_cannot_create_payment_method_without_required_fields(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post('/admin/payment-methods', [
            'name' => '',
            'bank_name' => '',
            'account_number' => '',
            'account_name' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'bank_name', 'account_number', 'account_name']);
    }

    public function test_admin_can_view_edit_payment_method_form(): void
    {
        $admin = $this->createAdminUser();
        $method = PaymentMethod::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/payment-methods/{$method->id}/edit");

        $response->assertOk();
        $response->assertSee($method->name);
    }

    public function test_admin_can_update_payment_method(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $method = PaymentMethod::factory()->create([
            'name' => 'Nama Lama',
            'bank_name' => 'Bank Lama',
        ]);

        $response = $this->actingAs($admin)->put("/admin/payment-methods/{$method->id}", [
            'name' => 'Nama Baru',
            'bank_name' => 'Bank Baru',
            'account_number' => '0987654321',
            'account_name' => 'Nama Rekening Baru',
            'instructions' => 'Instruksi baru',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response->assertRedirect('/admin/payment-methods');
        $this->assertDatabaseHas('payment_methods', [
            'id' => $method->id,
            'name' => 'Nama Baru',
            'bank_name' => 'Bank Baru',
        ]);
    }

    public function test_admin_can_update_payment_method_with_new_logo(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $oldLogo = UploadedFile::fake()->image('old_logo.png');
        $method = PaymentMethod::factory()->create([
            'logo' => $oldLogo->store('payment_methods', 'public'),
        ]);

        $newLogo = UploadedFile::fake()->image('new_logo.png');

        $response = $this->actingAs($admin)->put("/admin/payment-methods/{$method->id}", [
            'name' => $method->name,
            'bank_name' => $method->bank_name,
            'account_number' => $method->account_number,
            'account_name' => $method->account_name,
            'logo' => $newLogo,
            'instructions' => $method->instructions,
            'sort_order' => $method->sort_order,
            'is_active' => $method->is_active,
        ]);

        $response->assertRedirect('/admin/payment-methods');
        $this->assertDatabaseHas('payment_methods', [
            'id' => $method->id,
        ]);
    }

    public function test_admin_can_delete_payment_method(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $method = PaymentMethod::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/payment-methods/{$method->id}");

        $response->assertRedirect('/admin/payment-methods');
        $this->assertSoftDeleted('payment_methods', [
            'id' => $method->id,
        ]);
    }

    public function test_admin_can_toggle_payment_method_active_status(): void
    {
        $admin = $this->createAdminUser();
        $method = PaymentMethod::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch("/admin/payment-methods/{$method->id}/toggle");

        $response->assertRedirect();
        $method->refresh();
        $this->assertFalse($method->is_active);
    }

    public function test_non_admin_cannot_access_payment_methods(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get('/admin/payment-methods');

        $response->assertRedirect('/user/dashboard');
    }
}
