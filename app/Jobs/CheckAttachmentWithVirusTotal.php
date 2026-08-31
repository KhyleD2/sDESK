<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Models\ThreatReport;
use App\Services\VirusTotalService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckAttachmentWithVirusTotal implements ShouldQueue
{
    use Queueable;

    /**
     * The attachment to check
     */
    public $attachment;

    /**
     * Create a new job instance.
     */
    public function __construct(Attachment $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Execute the job.
     */
    public function handle(VirusTotalService $virusTotalService): void
    {
        Log::info('CheckAttachmentWithVirusTotal job started', [
            'attachment_id' => $this->attachment->id,
            'report_id' => $this->attachment->report_id,
            'file_hash' => substr($this->attachment->file_hash ?? 'none', 0, 16)
        ]);
        
        // Skip if attachment has no file hash
        if (empty($this->attachment->file_hash)) {
            Log::info('Attachment has no file hash, skipping VirusTotal check', [
                'attachment_id' => $this->attachment->id
            ]);
            return;
        }

        // Check the file hash with VirusTotal
        Log::info('Calling VirusTotal service', [
            'attachment_id' => $this->attachment->id
        ]);
        
        $scanResult = $virusTotalService->checkFileHash($this->attachment->file_hash);

        Log::info('VirusTotal service returned', [
            'attachment_id' => $this->attachment->id,
            'result_is_null' => $scanResult === null,
            'result' => $scanResult
        ]);

        // If no result (API failure), don't update the report
        if ($scanResult === null) {
            Log::warning('VirusTotal check returned null, not updating report', [
                'attachment_id' => $this->attachment->id,
                'report_id' => $this->attachment->report_id
            ]);
            return;
        }

        // Format result with filename
        $formattedResult = $virusTotalService->formatScanResult(
            $this->attachment->original_filename,
            $scanResult
        );

        Log::info('Formatted scan result', [
            'attachment_id' => $this->attachment->id,
            'formatted_result' => $formattedResult
        ]);

        // Load the parent report
        $report = ThreatReport::find($this->attachment->report_id);
        
        if (!$report) {
            Log::error('Parent report not found for attachment', [
                'attachment_id' => $this->attachment->id,
                'report_id' => $this->attachment->report_id
            ]);
            return;
        }

        // Append to existing scan_result or create new
        if (empty($report->scan_result)) {
            $report->scan_result = $formattedResult;
        } else {
            // Append with line break if there are multiple attachments
            $report->scan_result .= "\n" . $formattedResult;
        }

        $report->save();

        Log::info('VirusTotal scan result saved to report', [
            'attachment_id' => $this->attachment->id,
            'report_id' => $report->id,
            'result' => $scanResult,
            'report_scan_result' => $report->scan_result
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('VirusTotal check job failed', [
            'attachment_id' => $this->attachment->id,
            'error' => $exception->getMessage()
        ]);
    }
}
