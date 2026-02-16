<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Erick Mburu',
            'email' => 'mburuerick29@gmail.com',
            'password' => Hash::make('ErickAdmin2024'), // Secure password
            'user_type' => 'admin',
            'phone' => '+254708231994',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user for Erick Mburu created successfully!');
    }
}
