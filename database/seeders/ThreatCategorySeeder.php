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
            'Phishing',
            'Malware/Suspicious Files',
            'Suspicious URLs/Websites',
            'Social Engineering',
            'Data Leak/Exposure',
        ];

        foreach ($categories as $categoryName) {
            \App\Models\ThreatCategory::firstOrCreate(
                ['name' => $categoryName],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
