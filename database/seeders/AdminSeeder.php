<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing admin users first
        User::where('is_admin', true)->delete();

        // Create fresh admin
        $admin1 = User::firstOrNew(['email' => 'admin@gmail.com']);
        $admin1->forceFill([
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();

        echo "Admin created: admin@gmail.com / password123\n";
    }
}
