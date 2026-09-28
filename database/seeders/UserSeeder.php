<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Account (Your Account)
        \App\Models\User::create([
            'name' => 'Khyle Drey',
            'email' => 'khyle.drey@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Analyst Account
        \App\Models\User::create([
            'name' => 'Security Analyst',
            'email' => 'analyst@sentrydesk.local',
            'password' => \Illuminate\Support\Facades\Hash::make('analyst123'),
            'role' => 'analyst',
            'email_verified_at' => now(),
        ]);

        // Create Regular User Account
        \App\Models\User::create([
            'name' => 'Regular User',
            'email' => 'user@sentrydesk.local',
            'password' => \Illuminate\Support\Facades\Hash::make('user123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        echo "\n✅ Created 3 demo accounts:\n";
        echo "   Admin: khyle.drey@gmail.com / admin123\n";
        echo "   Analyst: analyst@sentrydesk.local / analyst123\n";
        echo "   User: user@sentrydesk.local / user123\n\n";
    }
}
