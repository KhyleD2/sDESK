<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'meiyelep@gmail.com',
            'password' => Hash::make('admin@123'),
            'role' => 'admin',
        ]);

        // Analyst User
        User::create([
            'name' => 'Analyst User',
            'email' => 'khyleazix@gmail.com',
            'password' => Hash::make('analyst@123'),
            'role' => 'analyst',
        ]);

        // Regular User
        User::create([
            'name' => 'Regular User',
            'email' => 'khyle.drey@gmail.com',
            'password' => Hash::make('user@123'),
            'role' => 'user',
        ]);
    }
}
