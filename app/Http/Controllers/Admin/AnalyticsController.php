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
        $weeksAgo = 12;
        $endWeek = now();
        $startWeek = now()->subWeeks($weeksAgo - 1);
        
        // Get actual resolution time data
        $actualResolutionData = ThreatReport::whereNotNull('updated_at')
            ->where('status', 'resolved')
            ->where('created_at', '>=', $startWeek)
            ->select(
                DB::raw('YEARWEEK(created_at, 3) as week_key'),
                DB::raw('DATE(created_at - INTERVAL (WEEKDAY(created_at)) DAY) as week_start'),
                DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            )
            ->groupBy('week_key', 'week_start')
            ->orderBy('week_key')
            ->get()
            ->keyBy('week_key');
        
        // Generate all 12 weeks and fill with 0 for missing data
        $resolutionTimeTrend = collect();
        for ($i = $weeksAgo - 1; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekKey = $weekStart->format('oW'); // ISO week format
            $weekLabel = $weekStart->format('M j'); // e.g., "Aug 19"
            
            $resolutionTimeTrend->push([
                'week' => $weekLabel,
                'avg_days' => $actualResolutionData->has($weekKey) 
                    ? (float) $actualResolutionData->get($weekKey)->avg_days 
                    : 0
            ]);
        }

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
        $avgResolutionTime = ThreatReport::where('status', 'resolved')
            ->whereNotNull('updated_at')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');

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
