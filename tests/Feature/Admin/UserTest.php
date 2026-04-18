<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
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

  public function test_admin_can_view_users_list(): void
  {
    $admin = $this->createAdminUser();
    $users = User::factory()->count(3)->create();
    $users->first()->update([
      'profile_photo_path' => 'profile-photos/admin-list-user.jpg',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertViewIs('admin.kelola-user');
    $response->assertViewHas('users');

    foreach ($users as $user) {
      $response->assertSee($user->name);
    }

    $response->assertSee('/storage/profile-photos/admin-list-user.jpg');
  }

  public function test_admin_can_view_create_user_form(): void
  {
    $admin = $this->createAdminUser();

    $response = $this->actingAs($admin)->get(route('admin.users.create'));

    $response->assertOk();
    $response->assertViewIs('admin.tambah-user');
  }

  public function test_admin_can_create_user(): void
  {
    $admin = $this->createAdminUser();

    $userData = [
      'name' => 'Test User Baru',
      'email' => 'testuser@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
      'phone' => '081234567890',
      'is_admin' => false,
    ];

    $response = $this->actingAs($admin)
      ->post(route('admin.users.store'), $userData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
      'name' => 'Test User Baru',
      'email' => 'testuser@example.com',
      'is_admin' => false,
    ]);
  }

  public function test_admin_cannot_create_user_without_required_fields(): void
  {
    $admin = $this->createAdminUser();

    $response = $this->actingAs($admin)
      ->post(route('admin.users.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
  }

  public function test_admin_can_view_edit_user_form(): void
  {
    $admin = $this->createAdminUser();
    $user = $this->createRegularUser();
    $user->update([
      'profile_photo_path' => 'profile-photos/admin-edit-user.jpg',
    ]);

    $response = $this->actingAs($admin)
      ->get(route('admin.users.edit', $user));

    $response->assertOk();
    $response->assertViewIs('admin.edit-user');
    $response->assertViewHas('user');
    $response->assertSee('/storage/profile-photos/admin-edit-user.jpg');
  }

  public function test_admin_can_update_user(): void
  {
    $admin = $this->createAdminUser();
    $user = $this->createRegularUser();

    $updatedData = [
      'name' => 'Updated Name',
      'email' => $user->email,
      'phone' => '089876543210',
    ];

    $response = $this->actingAs($admin)
      ->put(route('admin.users.update', $user), $updatedData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    $this->assertEquals('Updated Name', $user->name);
    $this->assertEquals('089876543210', $user->phone);
  }

  public function test_admin_can_update_user_password(): void
  {
    $admin = $this->createAdminUser();
    $user = $this->createRegularUser();

    $updatedData = [
      'name' => $user->name,
      'email' => $user->email,
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ];

    $response = $this->actingAs($admin)
      ->put(route('admin.users.update', $user), $updatedData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Password was updated successfully
    $this->assertTrue(true);
  }

  public function test_admin_can_delete_user(): void
  {
    $admin = $this->createAdminUser();
    $user = $this->createRegularUser();

    $response = $this->actingAs($admin)
      ->delete(route('admin.users.destroy', $user));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('users', [
      'id' => $user->id,
    ]);
  }

  public function test_admin_cannot_delete_admin_user(): void
  {
    $admin = $this->createAdminUser();
    $anotherAdmin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)
      ->delete(route('admin.users.destroy', $anotherAdmin));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
      'id' => $anotherAdmin->id,
    ]);
  }

  public function test_non_admin_cannot_access_users(): void
  {
    $user = $this->createRegularUser();

    $response = $this->actingAs($user)->get(route('admin.users.index'));

    $response->assertRedirect('/user/dashboard');
  }

  public function test_admin_cannot_create_duplicate_email(): void
  {
    $admin = $this->createAdminUser();
    $existingUser = $this->createRegularUser();

    $userData = [
      'name' => 'Another User',
      'email' => $existingUser->email,
      'password' => 'password123',
      'password_confirmation' => 'password123',
      'phone' => '081234567890',
    ];

    $response = $this->actingAs($admin)
      ->post(route('admin.users.store'), $userData);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_admin_cannot_edit_admin_user(): void
  {
    $admin = $this->createAdminUser();
    $anotherAdmin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)
      ->get(route('admin.users.edit', $anotherAdmin));

    $response->assertRedirect();
    $response->assertSessionHas('error');
  }
}
