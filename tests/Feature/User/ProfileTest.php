<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
  use RefreshDatabase;

  private User $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = User::factory()->create([
      'is_admin' => false,
      'password' => Hash::make('oldpassword123'),
    ]);
  }

  public function test_user_can_view_profile(): void
  {
    $this->actingAs($this->user);

    $response = $this->get(route('user.profil.show'));

    $response->assertStatus(200);
    $response->assertViewIs('user.profil');
    $response->assertViewHas('user', $this->user);
  }

  public function test_user_can_update_profile(): void
  {
    $this->actingAs($this->user);

    $updateData = [
      'name' => 'Nama Baru',
      'email' => 'emailbaru@example.com',
      'phone' => '081234567891',
      'address' => 'Alamat baru lengkap',
    ];

    $response = $this->put(route('user.profil.update'), $updateData);

    $response->assertRedirect(route('user.profil.show'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
      'id' => $this->user->id,
      'name' => 'Nama Baru',
      'email' => 'emailbaru@example.com',
      'phone' => '081234567891',
      'address' => 'Alamat baru lengkap',
    ]);
  }

  public function test_user_can_update_password(): void
  {
    $this->actingAs($this->user);

    $passwordData = [
      'current_password' => 'oldpassword123',
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ];

    $response = $this->put(route('user.profil.password'), $passwordData);

    $response->assertRedirect(route('user.profil.show'));
    $response->assertSessionHas('success');

    // Verify password was updated
    $this->user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $this->user->password));
  }

  public function test_user_cannot_update_password_with_wrong_current(): void
  {
    $this->actingAs($this->user);

    $passwordData = [
      'current_password' => 'wrongpassword',
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ];

    $response = $this->put(route('user.profil.password'), $passwordData);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Verify password was NOT updated
    $this->user->refresh();
    $this->assertTrue(Hash::check('oldpassword123', $this->user->password));
    $this->assertFalse(Hash::check('newpassword123', $this->user->password));
  }

  public function test_email_must_be_unique_except_self(): void
  {
    $this->actingAs($this->user);

    $otherUser = User::factory()->create([
      'email' => 'other@example.com',
    ]);

    // Try to change email to another user's email
    $response = $this->put(route('user.profil.update'), [
      'name' => $this->user->name,
      'email' => 'other@example.com',
      'phone' => $this->user->phone,
    ]);

    $response->assertSessionHasErrors('email');
  }

  public function test_user_can_keep_same_email(): void
  {
    $this->actingAs($this->user);

    $response = $this->put(route('user.profil.update'), [
      'name' => 'Nama Baru',
      'email' => $this->user->email,
      'phone' => $this->user->phone,
    ]);

    $response->assertRedirect(route('user.profil.show'));
    $response->assertSessionHas('success');
  }

  public function test_user_can_upload_profile_photo(): void
  {
    Storage::fake('public');

    $this->actingAs($this->user);

    $response = $this->put(route('user.profil.update'), [
      'name' => $this->user->name,
      'email' => $this->user->email,
      'phone' => $this->user->phone,
      'address' => $this->user->address,
      'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
    ]);

    $response->assertRedirect(route('user.profil.show'));
    $response->assertSessionHas('success');

    $this->user->refresh();

    $this->assertNotNull($this->user->profile_photo_path);
    Storage::disk('public')->assertExists($this->user->profile_photo_path);
  }

  public function test_user_can_delete_profile_photo(): void
  {
    Storage::fake('public');

    $this->user->update([
      'profile_photo_path' => 'profile-photos/existing-photo.jpg',
    ]);

    Storage::disk('public')->put('profile-photos/existing-photo.jpg', 'photo');

    $this->actingAs($this->user);

    $response = $this->delete(route('user.profil.photo.destroy'));

    $response->assertRedirect(route('user.profil.show'));
    $response->assertSessionHas('success');

    $this->user->refresh();

    $this->assertNull($this->user->profile_photo_path);
    Storage::disk('public')->assertMissing('profile-photos/existing-photo.jpg');
  }
}
