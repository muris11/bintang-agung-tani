<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user using forceFill to bypass fillable protection
        $admin1 = User::firstOrNew(['email' => 'admin@gmail.com']);
        $admin1->forceFill([
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();

        // Create additional admin
        $admin2 = User::firstOrNew(['email' => 'admin@bintangagung.com']);
        $admin2->forceFill([
            'name' => 'Admin Bintang Agung',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();
    }
}
