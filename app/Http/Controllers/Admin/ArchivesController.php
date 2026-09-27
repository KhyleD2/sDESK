<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThreatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivesController extends Controller
{
    /**
     * Display archived reports
     */
    public function index()
    {
        $archivedReports = ThreatReport::onlyTrashed()
            ->with(['user', 'category', 'attachments'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.archives', compact('archivedReports'));
    }

    /**
     * Restore an archived report
     */
    public function restore($id)
    {
        $report = ThreatReport::onlyTrashed()->findOrFail($id);
        
        $report->restore();
        
        // Clear archive metadata
        $report->archived_by = null;
        $report->archive_reason = null;
        $report->save();

        return redirect()->route('admin.archives')
            ->with('success', 'Report #' . $report->id . ' has been restored successfully.');
    }

    /**
     * Permanently delete an archived report
     */
    public function forceDelete($id)
    {
        $report = ThreatReport::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($report->attachments as $attachment) {
            if (Storage::disk('local')->exists($attachment->storage_path)) {
                Storage::disk('local')->delete($attachment->storage_path);
            }
        }

        // Permanently delete the report
        $report->forceDelete();

        return redirect()->route('admin.archives')
            ->with('success', 'Report has been permanently deleted.');
    }
}
