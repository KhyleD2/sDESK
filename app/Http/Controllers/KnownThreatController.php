<?php

namespace App\Http\Controllers;

use App\Models\KnownThreat;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnownThreatController extends Controller
{
    public function index(Request $request)
    {
        $query = KnownThreat::with(['firstReport', 'addedByUser']);

        // Search by indicator
        if ($request->filled('search')) {
            $query->where('indicator', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $threats = $query->orderBy('added_at', 'desc')->paginate(20)->withQueryString();

        return view('known-threats.index', compact('threats'));
    }

    public function store(Request $request)
    {
        // Only admins can add known threats
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can add known threats.');
        }

        $validated = $request->validate([
            'indicator' => ['required', 'string', 'unique:known_threats,indicator'],
            'type' => ['required', 'in:file_hash,url,ip,email,domain'],
            'first_reported_report_id' => ['nullable', 'exists:threat_reports,id'],
        ]);

        $threat = KnownThreat::create([
            'indicator' => $validated['indicator'],
            'type' => $validated['type'],
            'first_reported_report_id' => $validated['first_reported_report_id'] ?? null,
            'times_reported' => 1,
            'added_by' => Auth::id(),
        ]);

        // Log activity
        if ($validated['first_reported_report_id']) {
            ActivityLog::create([
                'report_id' => $validated['first_reported_report_id'],
                'user_id' => Auth::id(),
                'action_description' => 'Added to Known Threats: ' . $validated['type'] . ' - ' . $validated['indicator'],
            ]);
        }

        return back()->with('success', 'Known threat added successfully!');
    }

    public function destroy(KnownThreat $knownThreat)
    {
        // Only admins can delete known threats
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can delete known threats.');
        }

        $knownThreat->delete();

        return back()->with('success', 'Known threat removed successfully!');
    }
}
