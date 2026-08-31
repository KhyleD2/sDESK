<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\ThreatReport;
use Illuminate\Http\Request;

class ReportQueueController extends Controller
{
    public function index(Request $request)
    {
        $query = ThreatReport::with(['user', 'category', 'attachments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by verdict
        if ($request->filled('verdict')) {
            $query->where('verdict', $request->verdict);
        }

        // Sort options
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Special sort: Critical first
        if ($sortBy === 'severity') {
            $query->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low') ASC");
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $reports = $query->paginate(20)->withQueryString();

        return view('analyst.report-queue', compact('reports'));
    }

    /**
     * Show a specific report for review
     * 
     * SECURITY: Protected by middleware (role:analyst,admin)
     * Additional check: Ensure report exists and user has analyst/admin role
     */
    public function show(ThreatReport $report)
    {
        // Additional security: Verify user is analyst or admin
        // (middleware already checks this, but explicit check for defense in depth)
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isAnalyst()) {
            abort(403, 'Only analysts and admins can review reports.');
        }

        $report->load([
            'user',
            'category',
            'attachments',
            'actions.assignedUser',
            'activityLogs.user'
        ]);

        // Check if this indicator was reported before (community insights)
        $communityInsights = [];
        
        foreach ($report->attachments as $attachment) {
            if ($attachment->file_hash) {
                $previousReports = ThreatReport::whereHas('attachments', function ($query) use ($attachment) {
                    $query->where('file_hash', $attachment->file_hash);
                })->where('id', '!=', $report->id)->count();

                if ($previousReports > 0) {
                    $communityInsights[] = [
                        'type' => 'file_hash',
                        'indicator' => $attachment->file_hash,
                        'times_reported' => $previousReports + 1,
                    ];
                }
            }
        }

        return view('analyst.report-review', compact('report', 'communityInsights'));
    }

    /**
     * Update a report's details (analyst/admin action)
     * 
     * SECURITY: Protected by middleware (role:analyst,admin)
     * Additional check: Ensure user has analyst/admin role
     */
    public function update(Request $request, ThreatReport $report)
    {
        // Additional security: Verify user is analyst or admin
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isAnalyst()) {
            abort(403, 'Only analysts and admins can update reports.');
        }

        $validated = $request->validate([
            'severity' => ['nullable', 'in:low,medium,high,critical'],
            'status' => ['nullable', 'in:pending,under_review,in_progress,resolved'],
            'verdict' => ['nullable', 'in:pending,confirmed_threat,false_positive,escalated_externally'],
            'escalation_note' => ['nullable', 'string'],
            'scan_result' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:threat_categories,id'],
        ]);

        // Get original verdict before update
        $oldVerdict = $report->getOriginal('verdict');
        $oldStatus = $report->getOriginal('status');

        $report->update($validated);

        // Log changes
        $changes = [];
        if (isset($validated['status']) && $oldStatus !== $validated['status']) {
            $changes[] = "Status changed from {$oldStatus} to {$validated['status']}";
        }
        if (isset($validated['verdict']) && $oldVerdict !== $validated['verdict']) {
            $changes[] = "Verdict changed from {$oldVerdict} to {$validated['verdict']}";
        }

        if (!empty($changes)) {
            \App\Models\ActivityLog::create([
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'action_description' => implode('. ', $changes),
            ]);

            // Notify the report submitter
            \App\Models\Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'message' => 'Your report #' . $report->id . ' has been updated by ' . auth()->user()->name,
            ]);
        }

        // Handle escalation to external - specific notification and activity log
        if (isset($validated['verdict']) && 
            $validated['verdict'] === 'escalated_externally' && 
            $oldVerdict !== 'escalated_externally') {
            
            // Create specific activity log entry for escalation with the note
            $escalationDescription = auth()->user()->name . ' escalated this report externally';
            if (!empty($validated['escalation_note'])) {
                $escalationDescription .= ': ' . $validated['escalation_note'];
            }
            
            \App\Models\ActivityLog::create([
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'action_description' => $escalationDescription,
            ]);
            
            // Send specific notification to the report submitter about escalation
            \App\Models\Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'message' => 'Your report #' . $report->id . ' has been escalated externally by ' . auth()->user()->name,
            ]);
        }

        // Auto-add to Known Threats when verdict is changed TO 'confirmed_threat'
        if (isset($validated['verdict']) && 
            $validated['verdict'] === 'confirmed_threat' && 
            $oldVerdict !== 'confirmed_threat') {
            
            $this->addToKnownThreats($report);
        }

        return redirect()->route('analyst.report-queue.show', $report->id)
            ->with('success', 'Report updated successfully!');
    }

    /**
     * Add confirmed threat to Known Threats database
     */
    private function addToKnownThreats(ThreatReport $report)
    {
        // Load attachments if not already loaded
        $report->load('attachments');

        $indicator = null;
        $type = null;

        // Check if report has attachments with file hash
        if ($report->attachments->isNotEmpty()) {
            $firstAttachment = $report->attachments->first();
            if ($firstAttachment->file_hash) {
                $indicator = $firstAttachment->file_hash;
                $type = 'file_hash';
            }
        }

        // Only proceed if we found an indicator
        if ($indicator && $type) {
            // Use firstOrNew to find existing or create new
            $knownThreat = \App\Models\KnownThreat::firstOrNew(
                ['indicator' => $indicator]
            );

            if (!$knownThreat->exists) {
                // New threat - set all fields
                $knownThreat->type = $type;
                $knownThreat->first_reported_report_id = $report->id;
                $knownThreat->times_reported = 1;
                $knownThreat->added_by = auth()->id();
                $knownThreat->added_at = now();
            } else {
                // Existing threat - just increment counter
                $knownThreat->times_reported += 1;
            }

            $knownThreat->save();

            // Log this action
            \App\Models\ActivityLog::create([
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'action_description' => 'Added to Known Threats: ' . $type . ' - ' . substr($indicator, 0, 16) . '...',
            ]);
        }
    }
}
