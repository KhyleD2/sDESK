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
        // Create test users with known passwords
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sentrydesk.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Analyst User',
            'email' => 'analyst@sentrydesk.com',
            'password' => bcrypt('password'),
            'role' => 'analyst',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@sentrydesk.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->call([
            ThreatCategorySeeder::class,
        ]);
    }
}
