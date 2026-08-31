<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Download or view an attachment
     * 
     * SECURITY: IDOR Protection
     * - Loads the attachment's related report
     * - Only allows access if user is analyst/admin OR owns the report
     * - Prevents users from accessing other users' attachments by URL manipulation
     */
    public function download(Attachment $attachment, Request $request)
    {
        $user = Auth::user();
        
        // Eagerly load the report relationship to check ownership
        $attachment->load('report');
        $report = $attachment->report;
        
        // CRITICAL: Verify the report exists (orphaned attachments should not be accessible)
        if (!$report) {
            abort(404, 'Associated report not found.');
        }

        // Authorization check - user must be analyst/admin OR the report owner
        if (!$user->isAdmin() && !$user->isAnalyst() && $report->user_id !== $user->id) {
            abort(403, 'You do not have permission to view this file.');
        }

        // Check if file exists
        if (!Storage::disk('local')->exists($attachment->storage_path)) {
            abort(404, 'File not found.');
        }

        // Get the file path
        $filePath = Storage::disk('local')->path($attachment->storage_path);

        // Determine if we should display inline (for images) or force download
        $isImage = str_starts_with($attachment->file_type, 'image/');
        
        // If viewing (not downloading), display inline for images
        if ($request->query('view') && $isImage) {
            return response()->file($filePath, [
                'Content-Type' => $attachment->file_type,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_filename . '"'
            ]);
        }

        // Otherwise, force download
        return response()->download($filePath, $attachment->original_filename, [
            'Content-Type' => $attachment->file_type,
        ]);
    }
}
