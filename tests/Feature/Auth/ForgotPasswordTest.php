<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $response->assertSessionHas('reset_url');

        // Check token was created in database
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_forgot_password_requires_existing_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasInput('email', 'nonexistent@example.com');
    }

    public function test_forgot_password_requires_email_field(): void
    {
        $response = $this->post('/forgot-password', []);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_user_can_view_reset_password_page(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create a reset token
        $token = 'valid-token-123';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->get("/reset-password/{$token}?email=test@example.com");

        $response->assertOk();
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', 'test@example.com');
    }

    public function test_reset_password_page_shows_email_readonly(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $token = 'test-token-456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'user@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->get("/reset-password/{$token}?email=user@example.com");

        $response->assertOk();
        $response->assertSee('user@example.com');
        $response->assertSee('readonly');
    }

    public function test_user_can_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = 'valid-reset-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/user/dashboard');
        $response->assertSessionHas('success');

        // Password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));

        // Token was deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);

        // User is logged in
        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_password_requires_matching_confirmation(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_reset_password_requires_minimum_8_characters(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_reset_password_requires_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_requires_existing_email(): void
    {
        $token = 'some-token';

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'nonexistent@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_requires_all_fields(): void
    {
        $response = $this->post('/reset-password', []);

        $response->assertSessionHasErrors(['token', 'email', 'password']);
    }

    public function test_reset_password_rejects_expired_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = 'expired-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61), // Expired (60 min limit)
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/forgot-password');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_page_rejects_invalid_token(): void
    {
        $response = $this->get('/reset-password/invalid-token?email=test@example.com');

        $response->assertRedirect('/forgot-password');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_page_rejects_missing_email(): void
    {
        $response = $this->get('/reset-password/some-token');

        $response->assertRedirect('/forgot-password');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_page_rejects_expired_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = 'expired-token-view';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61),
        ]);

        $response = $this->get("/reset-password/{$token}?email=test@example.com");

        $response->assertRedirect('/forgot-password');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_forgot_password_creates_new_token_for_existing_email(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create existing token
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('old-token'),
            'created_at' => now()->subMinutes(30),
        ]);

        // Request new token
        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Should still have only one token (updated)
        $tokens = DB::table('password_reset_tokens')
            ->where('email', 'test@example.com')
            ->get();

        $this->assertCount(1, $tokens);
    }

    public function test_user_can_login_with_new_password_after_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = 'reset-and-login-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Reset password
        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'mynewpassword123',
            'password_confirmation' => 'mynewpassword123',
        ]);

        // Logout (simulated)
        $this->assertAuthenticated();
        auth()->logout();

        // Login with new password
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'mynewpassword123',
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_old_password_no_longer_works_after_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('originalpassword123'),
        ]);

        $token = 'change-password-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Reset password
        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'brandnewpassword123',
            'password_confirmation' => 'brandnewpassword123',
        ]);

        auth()->logout();

        // Try login with old password
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'originalpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}
