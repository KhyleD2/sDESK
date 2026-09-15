<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ThreatCategory;

class ThreatCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Malware',
            'Ransomware',
            'Phishing',
            'DDoS Attack',
            'Data Breach',
            'Insider Threat',
            'SQL Injection',
            'Cross-Site Scripting (XSS)',
            'Zero-Day Exploit',
            'Social Engineering',
            'Brute Force Attack',
            'Man-in-the-Middle',
            'Denial of Service',
            'Trojan',
            'Spyware',
            'Rootkit',
            'Botnet',
            'Password Attack',
            'Advanced Persistent Threat (APT)',
            'Cryptojacking',
        ];

        foreach ($categories as $category) {
            ThreatCategory::firstOrCreate(['name' => $category]);
        }
    }
}
