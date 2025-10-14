<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        \App\Models\User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@businessdb.ru',
                'password' => \Hash::make('admin123'),
                'role' => 'admin',
                'balance' => 1000.00,
                'email_verified_at' => now(),
                'email_verified' => true,
                'phone_verified' => true,
            ]
        );

        $this->command->info('Admin user created: username=admin / password=admin123');
    }
}
