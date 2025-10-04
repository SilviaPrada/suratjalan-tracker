<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kurir 1',
            'email' => 'kurir@example.com',
            'password' => Hash::make('password123'),
            'role' => 'kurir',
        ]);
    }
}
