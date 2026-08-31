<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThreatCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Phishing', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Malware/Suspicious Files', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suspicious URLs/Websites', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Social Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Data Leak/Exposure', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('threat_categories')->insert($categories);
    }
}
