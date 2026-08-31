<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ThreatReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // System-wide summary cards
        $totalReports = ThreatReport::count();
        $totalPending = ThreatReport::where('status', 'pending')->count();
        $inProgress = ThreatReport::where('status', 'in_progress')->count();
        $resolved = ThreatReport::where('status', 'resolved')->count();

        // Severity breakdown (currently open = not resolved)
        $severityBreakdown = ThreatReport::where('status', '!=', 'resolved')
            ->select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->get()
            ->pluck('count', 'severity')
            ->toArray();

        // User counts by role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();

        // Recent system activity (latest 15 actions across whole system)
        $recentActivity = ActivityLog::with(['user', 'report'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return view('admin.dashboard', compact(
            'totalReports',
            'totalPending',
            'inProgress',
            'resolved',
            'severityBreakdown',
            'usersByRole',
            'recentActivity'
        ));
    }
}
