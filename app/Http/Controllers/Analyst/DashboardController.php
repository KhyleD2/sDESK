<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\ActivityLog;
use App\Models\ThreatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Summary cards
        $pendingCount = ThreatReport::where('status', 'pending')->count();
        $underReviewCount = ThreatReport::where('status', 'under_review')->count();
        $inProgressCount = ThreatReport::where('status', 'in_progress')->count();
        $resolvedCount = ThreatReport::where('status', 'resolved')->count();
        
        // Today's reports
        $todayReports = ThreatReport::whereDate('created_at', today())->count();

        // Quick stats by severity
        $severityStats = ThreatReport::select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->get()
            ->pluck('count', 'severity')
            ->toArray();

        // Recent actions for activity feed
        $recentActions = Action::with(['report', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('analyst.dashboard', compact(
            'pendingCount',
            'underReviewCount',
            'inProgressCount',
            'resolvedCount',
            'todayReports',
            'severityStats',
            'recentActions'
        ));
    }
}
