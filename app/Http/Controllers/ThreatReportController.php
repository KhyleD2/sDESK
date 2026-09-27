<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\Notification;
use App\Models\ThreatCategory;
use App\Models\ThreatReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ThreatReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isAnalyst()) {
            $reports = ThreatReport::with(['user', 'category', 'attachments'])
                ->latest()
                ->paginate(20);
        } else {
            $reports = ThreatReport::with(['category', 'attachments'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(20);
        }

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        // Only regular users can submit reports
        if (!Auth::user()->isUser()) {
            abort(403, 'Only regular users can submit reports. Analysts and Admins manage and review reports.');
        }

        $categories = ThreatCategory::all();
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Only regular users can submit reports
        if (!Auth::user()->isUser()) {
            abort(403, 'Only regular users can submit reports. Analysts and Admins manage and review reports.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:threat_categories,id'],
            'description' => ['required', 'string', 'min:10'],
            'severity' => ['nullable', 'in:low,medium,high,critical'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // 10MB max
        ]);

        $report = ThreatReport::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'severity' => $validated['severity'] ?? 'low',
            'status' => 'pending',
            'verdict' => 'pending',
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalFilename = $file->getClientOriginalName();
                $storedFilename = uniqid() . '_' . $originalFilename;
                $path = $file->storeAs('threat_attachments', $storedFilename, 'local');
                $fileHash = hash_file('sha256', $file->getRealPath());

                $attachment = Attachment::create([
                    'report_id' => $report->id,
                    'original_filename' => $originalFilename,
                    'stored_filename' => $storedFilename,
                    'file_hash' => $fileHash,
                    'file_type' => $file->getMimeType(),
                    'storage_path' => $path,
                ]);

                // Dispatch VirusTotal check job (now runs synchronously with QUEUE_CONNECTION=sync)
                \Illuminate\Support\Facades\Log::info('Dispatching VirusTotal check job', [
                    'attachment_id' => $attachment->id,
                    'report_id' => $report->id,
                    'filename' => $originalFilename,
                    'hash' => substr($fileHash, 0, 16) . '...',
                    'queue_connection' => config('queue.default')
                ]);
                
                \App\Jobs\CheckAttachmentWithVirusTotal::dispatch($attachment);
                
                \Illuminate\Support\Facades\Log::info('VirusTotal check job dispatched', [
                    'attachment_id' => $attachment->id
                ]);
            }
        }

        // Log activity
        ActivityLog::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'action_description' => 'Threat report submitted',
        ]);

        // Notify analysts and admins
        $analysts = User::whereIn('role', ['analyst', 'admin'])->get();
        foreach ($analysts as $analyst) {
            Notification::create([
                'user_id' => $analyst->id,
                'report_id' => $report->id,
                'message' => 'New threat report #' . $report->id . ' submitted by ' . Auth::user()->name,
            ]);
        }

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Threat report submitted successfully!');
    }

    public function show(ThreatReport $report)
    {
        $user = Auth::user();

        // Check authorization
        if (!$user->isAdmin() && !$user->isAnalyst() && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this report.');
        }

        $report->load(['user', 'category', 'attachments', 'actions.assignedUser', 'activityLogs.user']);

        return view('reports.show', compact('report'));
    }

    public function edit(ThreatReport $report)
    {
        // Only analysts and admins can edit
        if (!Auth::user()->isAdmin() && !Auth::user()->isAnalyst()) {
            abort(403, 'Only analysts can edit reports.');
        }

        $categories = ThreatCategory::all();
        return view('reports.edit', compact('report', 'categories'));
    }

    public function update(Request $request, ThreatReport $report)
    {
        // Only analysts and admins can update
        if (!Auth::user()->isAdmin() && !Auth::user()->isAnalyst()) {
            abort(403, 'Only analysts can update reports.');
        }

        $validated = $request->validate([
            'severity' => ['nullable', 'in:low,medium,high,critical'],
            'status' => ['nullable', 'in:pending,under_review,in_progress,resolved'],
            'verdict' => ['nullable', 'in:pending,confirmed_threat,false_positive,escalated_externally'],
            'escalation_note' => ['nullable', 'string'],
            'scan_result' => ['nullable', 'string'],
        ]);

        $oldStatus = $report->status;
        $oldVerdict = $report->getOriginal('verdict'); // Get original value before update

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
            ActivityLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'action_description' => implode('. ', $changes),
            ]);

            // Notify the report submitter
            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'message' => 'Your report #' . $report->id . ' has been updated by ' . Auth::user()->name,
            ]);
        }

        // Handle escalation to external - specific notification and activity log
        if (isset($validated['verdict']) && 
            $validated['verdict'] === 'escalated_externally' && 
            $oldVerdict !== 'escalated_externally') {
            
            // Create specific activity log entry for escalation with the note
            $escalationDescription = Auth::user()->name . ' escalated this report externally';
            if (!empty($validated['escalation_note'])) {
                $escalationDescription .= ': ' . $validated['escalation_note'];
            }
            
            ActivityLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'action_description' => $escalationDescription,
            ]);
            
            // Send specific notification to the report submitter about escalation
            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'message' => 'Your report #' . $report->id . ' has been escalated externally by ' . Auth::user()->name,
            ]);
        }

        // Auto-add to Known Threats when verdict is changed TO 'confirmed_threat'
        if (isset($validated['verdict']) && 
            $validated['verdict'] === 'confirmed_threat' && 
            $oldVerdict !== 'confirmed_threat') {
            
            $this->addToKnownThreats($report);
        }

        return redirect()->route('reports.show', $report->id)
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
                $knownThreat->added_by = Auth::id();
                $knownThreat->added_at = now();
            } else {
                // Existing threat - just increment counter
                $knownThreat->times_reported += 1;
            }

            $knownThreat->save();

            // Log this action
            ActivityLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'action_description' => 'Added to Known Threats: ' . $type . ' - ' . substr($indicator, 0, 16) . '...',
            ]);
        }
    }

    public function destroy(ThreatReport $report)
    {
        $user = Auth::user();

        // Users can only archive their own reports, admins/analysts can archive any
        if (!$user->isAdmin() && !$user->isAnalyst() && $report->user_id !== $user->id) {
            abort(403, 'You can only remove your own reports.');
        }

        // Soft delete (archive) the report
        $report->archived_by = $user->name . ' (' . $user->role . ')';
        $report->archive_reason = $user->isUser() ? 'User removed report' : 'Archived by ' . $user->role;
        $report->save();
        $report->delete(); // Soft delete

        // Log activity
        ActivityLog::create([
            'report_id' => $report->id,
            'user_id' => $user->id,
            'action_description' => 'Report archived/removed by ' . $user->name,
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Report has been archived successfully.');
    }
}
