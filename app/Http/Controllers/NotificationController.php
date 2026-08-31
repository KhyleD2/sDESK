<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('report')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        // Ensure user owns this notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read!');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read!');
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * API endpoint to fetch recent notifications for dropdown
     */
    public function getRecent(Request $request)
    {
        $limit = $request->input('limit', 10);
        
        $notifications = Notification::with('report')
            ->where('user_id', Auth::id())
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'report_id' => $notification->report_id,
                    'report_url' => $notification->report_id ? route('analyst.report-queue.show', $notification->report_id) : null,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count()
        ]);
    }

    /**
     * API endpoint to mark single notification as read
     */
    public function markAsReadApi(Notification $notification)
    {
        // Ensure user owns this notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * API endpoint to mark all notifications as read
     */
    public function markAllAsReadApi()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
