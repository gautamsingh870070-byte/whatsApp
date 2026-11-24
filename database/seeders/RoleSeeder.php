<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'daily_limit' => 10000,
            ]
        );

        // 2. Create 2 Admins
        User::updateOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'First Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'daily_limit' => 5000,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Second Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'daily_limit' => 5000,
            ]
        );

        // 3. Create 2 Clients
        User::updateOrCreate(
            ['email' => 'client1@example.com'],
            [
                'name' => 'First Client',
                'password' => Hash::make('password'),
                'role' => 'client',
                'daily_limit' => 500,
            ]
        );

        User::updateOrCreate(
            ['email' => 'client2@example.com'],
            [
                'name' => 'Second Client',
                'password' => Hash::make('password'),
                'role' => 'client',
                'daily_limit' => 500,
            ]
        );
    }
}
