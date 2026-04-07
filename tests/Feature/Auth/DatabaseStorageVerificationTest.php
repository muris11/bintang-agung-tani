<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseStorageVerificationTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Verify admin user is stored in database, not hardcoded
     */
    public function test_admin_user_is_stored_in_database(): void
    {
        // Seed admin
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);

        // Verify admin exists in database
        $admin = User::where('email', 'admin@gmail.com')->first();
        
        $this->assertNotNull($admin, 'Admin user should exist in database');
        $this->assertEquals('admin@gmail.com', $admin->email);
        $this->assertEquals('Administrator', $admin->name);
        $this->assertTrue($admin->is_admin);
        $this->assertNotNull($admin->password);
        
        // Verify password is hashed in database
        $this->assertTrue(str_starts_with($admin->password, '$2y$') || str_starts_with($admin->password, '$argon'));
        
        // Verify password verification works
        $this->assertTrue(Hash::check('password123', $admin->password));
        
        // Show database record details
        dump([
            'id' => $admin->id,
            'email' => $admin->email,
            'name' => $admin->name,
            'is_admin' => $admin->is_admin,
            'email_verified_at' => $admin->email_verified_at,
            'password_hash_prefix' => substr($admin->password, 0, 30),
            'created_at' => $admin->created_at,
            'updated_at' => $admin->updated_at,
        ]);
    }

    /**
     * Verify login reads from database, not hardcoded
     */
    public function test_login_reads_credentials_from_database(): void
    {
        // Seed admin
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);

        // Get fresh user from database
        $admin = User::where('email', 'admin@gmail.com')->first();
        $this->assertNotNull($admin);

        // Login using form submission (reads from DB)
        $response = $this->post('/login', [
            'email' => 'admin@gmail.com',
            'password' => 'password123',
        ]);

        // Should redirect after successful login
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        
        // Verify authenticated user matches database record
        $this->assertEquals($admin->id, auth()->id());
        $this->assertEquals($admin->email, auth()->user()->email);
    }

    /**
     * Verify no hardcoded credentials in source code
     */
    public function test_no_hardcoded_admin_password_in_source(): void
    {
        // Check that password is not hardcoded in AuthController
        $authController = file_get_contents(app_path('Http/Controllers/AuthController.php'));
        $this->assertStringNotContainsString("'password123'", $authController);
        $this->assertStringNotContainsString('admin@gmail.com', $authController);

        // Check User model
        $userModel = file_get_contents(app_path('Models/User.php'));
        $this->assertStringNotContainsString("'password123'", $userModel);
        $this->assertStringNotContainsString('admin@gmail.com', $userModel);

        // Verify Auth::attempt is used (database lookup)
        $this->assertStringContainsString('Auth::attempt', $authController);
    }
}
