<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\ThreatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        try {
            // Debug: Log all reports with their status and timestamps
            $allReports = ThreatReport::select('id', 'status', 'created_at', 'updated_at')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();
            \Log::info("Analytics Debug - Last 10 Reports: " . $allReports->toJson());
            
            // 1. Reports by Category
            $reportsByCategory = ThreatReport::join('threat_categories', 'threat_reports.category_id', '=', 'threat_categories.id')
                ->select('threat_categories.name', DB::raw('count(*) as total'))
                ->groupBy('threat_categories.name')
                ->get();

        // 2. Severity Distribution
        $severityDistribution = ThreatReport::select('severity', DB::raw('count(*) as total'))
            ->whereNotNull('severity')
            ->groupBy('severity')
            ->get();

        // 3. Reports Over Time (last 30 days) - Generate all 30 days
        $startDate = now()->subDays(29)->startOfDay(); // Last 30 days including today
        $endDate = now()->endOfDay();
        
        // Get actual report counts
        $actualReports = ThreatReport::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date');
        
        // Generate all 30 dates and fill with 0 for missing dates
        $reportsOverTime = collect();
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays(29 - $i)->format('Y-m-d');
            $reportsOverTime->push([
                'date' => $date,
                'total' => $actualReports->get($date, 0)
            ]);
        }

        // 4. Verdict Breakdown
        $verdictBreakdown = ThreatReport::select('verdict', DB::raw('count(*) as total'))
            ->whereNotNull('verdict')
            ->groupBy('verdict')
            ->get();

        // 5. Resolution Time Trend (average days to resolve, last 12 weeks)
        $weeksAgo = 8; // Changed to 8 weeks to match the subtitle
        
        // Debug: Check resolved reports in the last 12 weeks
        $recentResolvedCount = ThreatReport::where('status', 'resolved')
            ->where('created_at', '>=', now()->subWeeks($weeksAgo))
            ->count();
        \Log::info("Analytics Debug - Resolved Reports (last {$weeksAgo} weeks): {$recentResolvedCount}");
        
        // Simplified approach: Get all resolved reports and group by week in PHP
        $resolvedReportsForTrend = ThreatReport::where('status', 'resolved')
            ->whereNotNull('updated_at')
            ->where('created_at', '>=', now()->subWeeks($weeksAgo))
            ->select('id', 'created_at', 'updated_at',
                DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at) / 24.0 as days_to_resolve'))
            ->get();
        
        \Log::info("Analytics Debug - Resolution Trend Data: " . $resolvedReportsForTrend->toJson());
        
        // Group by week and calculate averages
        $weeklyData = [];
        foreach ($resolvedReportsForTrend as $report) {
            $weekStart = $report->created_at->startOfWeek()->format('M j');
            if (!isset($weeklyData[$weekStart])) {
                $weeklyData[$weekStart] = [
                    'sum' => 0,
                    'count' => 0
                ];
            }
            $weeklyData[$weekStart]['sum'] += (float) $report->days_to_resolve;
            $weeklyData[$weekStart]['count']++;
        }
        
        // Generate all weeks and fill with data or 0
        $resolutionTimeTrend = collect();
        for ($i = $weeksAgo - 1; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekLabel = $weekStart->format('M j');
            
            $avgDays = 0;
            if (isset($weeklyData[$weekLabel]) && $weeklyData[$weekLabel]['count'] > 0) {
                $avgDays = round($weeklyData[$weekLabel]['sum'] / $weeklyData[$weekLabel]['count'], 2);
            }
            
            $resolutionTimeTrend->push([
                'week' => $weekLabel,
                'avg_days' => $avgDays
            ]);
        }
        
        \Log::info("Analytics Debug - Final Resolution Trend: " . $resolutionTimeTrend->toJson());

        // 6. Top Reported Indicators
        $topIndicators = \App\Models\KnownThreat::orderByDesc('times_reported')
            ->limit(5)
            ->get(['indicator', 'times_reported', 'type']);

        // 7. Status Overview
        $statusOverview = ThreatReport::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Overall summary stats
        $totalReports = ThreatReport::count();
        $confirmedThreats = ThreatReport::where('verdict', 'confirmed_threat')->count();
        $falsePositives = ThreatReport::where('verdict', 'false_positive')->count();
        
        // Debug: Check how many resolved reports we have
        $resolvedCount = ThreatReport::where('status', 'resolved')->count();
        \Log::info("Analytics Debug - Resolved Reports Count: {$resolvedCount}");
        
        // Get resolved reports with timestamps for debugging
        $resolvedReports = ThreatReport::where('status', 'resolved')
            ->select('id', 'created_at', 'updated_at', 
                DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at) as hours_diff'),
                DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at) / 24.0 as days_diff'))
            ->get();
        \Log::info("Analytics Debug - Resolved Reports Details: " . $resolvedReports->toJson());
        
        $avgResolutionTime = ThreatReport::where('status', 'resolved')
            ->whereNotNull('updated_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at) / 24.0) as avg_days')
            ->value('avg_days');
        
        \Log::info("Analytics Debug - Average Resolution Time: " . ($avgResolutionTime ?? 'NULL'));
        
        // Round to 2 decimal places
        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime, 2) : null;

        // Prepare data for Chart.js (encode as JSON)
        $analyticsData = [
            'reportsByCategory' => $reportsByCategory,
            'severityDistribution' => $severityDistribution,
            'reportsOverTime' => $reportsOverTime,
            'verdictBreakdown' => $verdictBreakdown,
            'resolutionTimeTrend' => $resolutionTimeTrend,
            'topIndicators' => $topIndicators,
            'statusOverview' => $statusOverview,
        ];

        return view('admin.analytics', compact(
            'analyticsData',
            'totalReports',
            'confirmedThreats',
            'falsePositives',
            'avgResolutionTime'
        ));
        } catch (\Exception $e) {
            \Log::error('Analytics Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load analytics: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        if ($format === 'csv') {
            return $this->exportCSV();
        }

        // PDF export would go here (requires additional package like dompdf)
        return back()->with('error', 'PDF export not implemented yet');
    }

    private function exportCSV()
    {
        $reports = ThreatReport::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'threat_reports_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');

            // CSV header
            fputcsv($file, ['ID', 'Submitted By', 'Email', 'Category', 'Severity', 'Status', 'Verdict', 'Created At', 'Resolved At']);

            // Data rows
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->id,
                    $report->user->name,
                    $report->user->email,
                    $report->category->name,
                    $report->severity,
                    $report->status,
                    $report->verdict,
                    $report->created_at->format('Y-m-d H:i:s'),
                    $report->status === 'resolved' ? $report->updated_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
