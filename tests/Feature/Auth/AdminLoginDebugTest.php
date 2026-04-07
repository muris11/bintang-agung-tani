<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginDebugTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_with_seeded_credentials(): void
    {
        // Seed the admin
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);

        // Verify user exists
        $user = User::where('email', 'admin@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);

        // Test login - should redirect (302) on success
        $response = $this->post('/login', [
            'email' => 'admin@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302); // Redirect after successful login
        $response->assertRedirect('/admin/dashboard'); // Admin should redirect to admin dashboard
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->is_admin);
    }

    public function test_admin_can_access_dashboard(): void
    {
        // Seed the admin
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);

        // Login
        $response = $this->postJson('/login', [
            'email' => 'admin@gmail.com',
            'password' => 'password123',
        ]);

        // Access admin dashboard
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
