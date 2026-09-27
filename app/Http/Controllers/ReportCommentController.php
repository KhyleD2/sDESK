<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ReportComment;
use App\Models\ThreatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportCommentController extends Controller
{
    public function store(Request $request, ThreatReport $report)
    {
        // Authorization: Users can only comment on their own reports, analysts/admins can comment on any
        if (Auth::user()->isUser() && $report->user_id !== Auth::id()) {
            abort(403, 'You can only comment on your own reports.');
        }

        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        $comment = ReportComment::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        // Notify the other party
        // If analyst comments, notify the user
        // If user comments, notify all analysts/admins
        if (Auth::user()->isUser()) {
            // Notify analysts and admins
            $analysts = \App\Models\User::whereIn('role', ['analyst', 'admin'])->get();
            foreach ($analysts as $analyst) {
                Notification::create([
                    'user_id' => $analyst->id,
                    'report_id' => $report->id,
                    'message' => Auth::user()->name . ' commented on report #' . $report->id,
                ]);
            }
        } else {
            // Notify the report owner
            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'message' => Auth::user()->name . ' commented on your report #' . $report->id,
            ]);
        }

        return back()->with('success', 'Comment posted successfully!');
    }
}
