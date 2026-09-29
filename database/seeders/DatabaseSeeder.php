<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update admin account with your email
        User::updateOrCreate(
            ['email' => 'khyle.drey@gmail.com'],
            [
                'name' => 'Khyle Drey',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create or update analyst account
        User::updateOrCreate(
            ['email' => 'analyst@sentrydesk.local'],
            [
                'name' => 'Security Analyst',
                'password' => bcrypt('analyst123'),
                'role' => 'analyst',
                'email_verified_at' => now(),
            ]
        );

        // Create or update regular user account
        User::updateOrCreate(
            ['email' => 'user@sentrydesk.local'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Seed threat categories
        $this->call([
            ThreatCategorySeeder::class,
        ]);

        echo "\n✅ Seeding complete!\n";
        echo "   Admin: khyle.drey@gmail.com / admin123\n";
        echo "   Analyst: analyst@sentrydesk.local / analyst123\n";
        echo "   User: user@sentrydesk.local / user123\n\n";
    }
}
