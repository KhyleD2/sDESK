<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ThreatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // My Reports Summary
        $myTotalReports = ThreatReport::where('user_id', $user->id)->count();
        $myPending = ThreatReport::where('user_id', $user->id)->where('status', 'pending')->count();
        $myUnderReview = ThreatReport::where('user_id', $user->id)->where('status', 'under_review')->count();
        $myInProgress = ThreatReport::where('user_id', $user->id)->where('status', 'in_progress')->count();
        $myResolved = ThreatReport::where('user_id', $user->id)->where('status', 'resolved')->count();

        // My Reports by Severity
        $mySeverityStats = ThreatReport::where('user_id', $user->id)
            ->select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->get()
            ->pluck('count', 'severity')
            ->toArray();

        // My Recent Reports (last 5)
        $myRecentReports = ThreatReport::with(['category'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Unread Notifications Count
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Recent Notifications (last 5)
        $recentNotifications = Notification::with('report')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'myTotalReports',
            'myPending',
            'myUnderReview',
            'myInProgress',
            'myResolved',
            'mySeverityStats',
            'myRecentReports',
            'unreadNotifications',
            'recentNotifications'
        ));
    }
}
