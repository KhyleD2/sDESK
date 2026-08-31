<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VirusTotalService
{
    /**
     * Check a file hash against VirusTotal API
     * 
     * @param string $hash SHA256 hash of the file
     * @return string|null Formatted scan result or null on failure
     */
    public function checkFileHash(string $hash): ?string
    {
        $apiKey = config('services.virustotal.api_key');
        
        Log::info('VirusTotal check started', [
            'hash' => substr($hash, 0, 16) . '...',
            'api_key_configured' => !empty($apiKey)
        ]);
        
        // If no API key configured, return null silently
        if (empty($apiKey)) {
            Log::warning('VirusTotal API key not configured');
            return null;
        }

        try {
            // Call VirusTotal API v3 with timeout
            Log::info('Calling VirusTotal API', ['url' => "https://www.virustotal.com/api/v3/files/{$hash}"]);
            
            $response = Http::timeout(8)
                ->withHeaders([
                    'x-apikey' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("https://www.virustotal.com/api/v3/files/{$hash}");

            Log::info('VirusTotal API response received', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            // File hash found in VirusTotal database
            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('VirusTotal response data', [
                    'has_data' => isset($data['data']),
                    'has_attributes' => isset($data['data']['attributes']),
                    'has_stats' => isset($data['data']['attributes']['last_analysis_stats'])
                ]);
                
                // Extract detection statistics
                $stats = $data['data']['attributes']['last_analysis_stats'] ?? null;
                
                if ($stats) {
                    $malicious = $stats['malicious'] ?? 0;
                    $suspicious = $stats['suspicious'] ?? 0;
                    $harmless = $stats['harmless'] ?? 0;
                    $undetected = $stats['undetected'] ?? 0;
                    
                    // Total engines = sum of all categories
                    $totalEngines = $malicious + $suspicious + $harmless + $undetected;
                    
                    // Build result string
                    $result = "VirusTotal: {$malicious}/{$totalEngines} vendors flagged as malicious";
                    
                    // Add suspicious count if any
                    if ($suspicious > 0) {
                        $result .= ", {$suspicious} suspicious";
                    }
                    
                    Log::info('VirusTotal scan result formatted', [
                        'result' => $result,
                        'malicious' => $malicious,
                        'total' => $totalEngines
                    ]);
                    
                    return $result;
                }
                
                Log::warning('VirusTotal response missing statistics');
                return "VirusTotal: Scan completed but no statistics available";
            }
            
            // File hash not found (404) - file never scanned before
            if ($response->status() === 404) {
                Log::info('VirusTotal: File hash not in database (404)');
                return "VirusTotal: No existing record for this file (not yet scanned by any engine)";
            }
            
            // Rate limit or other error
            if ($response->status() === 429) {
                Log::warning('VirusTotal rate limit exceeded', ['hash' => substr($hash, 0, 16)]);
                return "VirusTotal: Rate limit exceeded, scan pending";
            }
            
            // Other HTTP errors
            Log::warning('VirusTotal API returned error', [
                'status' => $response->status(),
                'hash' => substr($hash, 0, 16),
                'body' => substr($response->body(), 0, 500)
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            // Network error, timeout, or other exception
            Log::error('VirusTotal API call exception', [
                'hash' => substr($hash, 0, 16),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Format file scan result with filename for display
     * 
     * @param string $filename Original filename
     * @param string $scanResult Scan result from checkFileHash
     * @return string Formatted result
     */
    public function formatScanResult(string $filename, string $scanResult): string
    {
        return "[{$filename}] {$scanResult}";
    }
}
