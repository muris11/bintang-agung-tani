<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular user
        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Create additional users
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@email.com',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@email.com',
            ],
            [
                'name' => 'Dewi Kusuma',
                'email' => 'dewi@email.com',
            ],
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus@email.com',
            ],
            [
                'name' => 'Rini Susanti',
                'email' => 'rini@email.com',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password123'),
                    'is_admin' => false,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
