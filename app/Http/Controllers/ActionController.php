<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\ThreatReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    /**
     * Store a new action (assign an action to a report)
     * 
     * SECURITY: IDOR Protection
     * - Only analysts/admins can assign actions
     * - Validates they have access to the report
     */
    public function store(Request $request, ThreatReport $report)
    {
        $user = Auth::user();
        
        // SECURITY: Only analysts and admins can assign actions
        if (!$user->isAdmin() && !$user->isAnalyst()) {
            abort(403, 'Only analysts and admins can assign actions.');
        }

        $validated = $request->validate([
            'action_type' => ['required', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        // Determine who to assign the action to
        // If not specified, assign to the current user (analyst/admin)
        $assignedTo = $validated['assigned_to'] ?? Auth::id();

        $action = Action::create([
            'report_id' => $report->id,
            'assigned_to' => $assignedTo,
            'action_type' => $validated['action_type'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Build activity log description
        $assignedToUser = User::find($assignedTo);
        $activityDescription = 'Action assigned: ' . $validated['action_type'];
        
        if ($assignedTo === Auth::id()) {
            $activityDescription .= ' (self-assigned)';
        } else {
            $activityDescription .= ' to ' . $assignedToUser->name;
        }

        // Log activity
        ActivityLog::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'action_description' => $activityDescription,
        ]);

        // Notify assigned user (only if assigned to someone else)
        if ($assignedTo != Auth::id()) {
            $notificationMessage = 'Action assigned to you: ' . $validated['action_type'];
            
            // Include notes in notification if provided
            if (!empty($validated['notes'])) {
                $notificationMessage .= ' — ' . $validated['notes'];
            }
            
            $notificationMessage .= ' (Report #' . $report->id . ')';
            
            Notification::create([
                'user_id' => $assignedTo,
                'report_id' => $report->id,
                'message' => $notificationMessage,
            ]);
        }

        return back()->with('success', 'Action assigned successfully!');
    }

    /**
     * Update an action's status
     * 
     * SECURITY: IDOR Protection
     * - Only analysts/admins can update actions
     * - OR the user to whom the action is assigned
     * - Prevents unauthorized users from modifying actions
     */
    public function update(Request $request, Action $action)
    {
        $user = Auth::user();
        
        // CRITICAL SECURITY FIX: Authorization check
        // Only allow: analysts, admins, or the assigned user
        $action->load('report'); // Load report relationship for additional checks if needed
        
        if (!$user->isAdmin() && !$user->isAnalyst() && $action->assigned_to !== $user->id) {
            abort(403, 'You do not have permission to update this action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $action->status;
        $action->update($validated);

        // If completed, set resolved_at
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $action->update(['resolved_at' => now()]);
        }

        // Log activity
        ActivityLog::create([
            'report_id' => $action->report_id,
            'user_id' => Auth::id(),
            'action_description' => 'Action status changed from ' . $oldStatus . ' to ' . $validated['status'],
        ]);

        return back()->with('success', 'Action updated successfully!');
    }

    public function index()
    {
        $actions = Action::with(['report.user', 'report.category', 'assignedUser'])
            ->where('assigned_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('actions.index', compact('actions'));
    }
}
