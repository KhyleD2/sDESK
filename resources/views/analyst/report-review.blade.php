@extends('layouts.app')

@section('title', 'Report Review #'.$report->id.' - SentryDesk')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<style>
    .review-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0;
    }
    
    .page-header {
        margin-bottom: 16px;
    }
    
    .page-title {
        font-size: 30px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    
    .page-subtitle {
        color: var(--text-faint);
        font-size: 14px;
        font-weight: 400;
    }
    
    /* Community Insights Alert */
    .insights-alert {
        background: var(--amber-dim);
        border: 1px solid var(--amber);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .insights-alert-title {
        color: var(--amber);
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .insights-alert p {
        color: var(--text);
        font-size: 14px;
        margin: 6px 0;
    }
    
    /* Card Styles */
    .review-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .review-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
        letter-spacing: -0.3px;
    }
    
    .detail-row {
        margin-bottom: 16px;
    }
    
    .detail-label {
        color: var(--text-dim);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    
    .detail-value {
        color: var(--text);
        font-size: 14px;
    }
    
    .description-box {
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 16px;
        color: var(--text);
        font-size: 14px;
        line-height: 1.6;
        white-space: pre-wrap;
        margin-top: 8px;
    }
    
    /* Attachments */
    .attachment-item {
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        transition: border-color 0.2s;
    }
    
    .attachment-item:hover {
        border-color: var(--line-soft);
    }
    
    .attachment-filename {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 6px;
    }
    
    .attachment-meta {
        color: var(--text-dim);
        font-size: 12px;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .attachment-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .view-btn {
        background: var(--cyan-dim);
        color: var(--cyan);
    }
    
    .view-btn:hover {
        background: rgba(52, 228, 214, 0.18);
    }
    
    .download-btn {
        background: var(--success-dim);
        color: var(--success);
    }
    
    .download-btn:hover {
        background: rgba(61, 214, 140, 0.18);
    }
    
    /* Form Styles */
    .assessment-form {
        background: var(--bg-card);
        border: 2px solid var(--cyan);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .assessment-form {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 20px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-label {
        color: var(--text);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .form-select,
    .form-textarea {
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 11px 14px;
        color: var(--text);
        font-size: 14px;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
    }
    
    .form-textarea {
        resize: vertical;
        line-height: 1.5;
    }
    
    .form-textarea::placeholder {
        color: var(--text-faint);
    }
    
    .form-hint {
        color: var(--text-dim);
        font-size: 12px;
        margin-top: 6px;
    }
    
    .form-select option {
        background: var(--bg);
        color: var(--text);
    }
    
    /* Buttons */
    .btn-primary {
        background: var(--cyan);
        color: var(--bg);
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary:hover {
        opacity: 0.9;
    }
    
    .btn-secondary {
        background: var(--line-soft);
        color: var(--text);
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        opacity: 0.8;
    }
    
    .btn-success {
        background: var(--success);
        color: var(--bg);
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .btn-success:hover {
        opacity: 0.9;
    }
    
    /* Action Assignment Section */
    .action-card {
        background: var(--bg-card);
        border: 1px solid var(--cyan);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .action-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .form-input {
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 11px 14px;
        color: var(--text);
        font-size: 14px;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
    }
    
    .form-input::placeholder {
        color: var(--text-faint);
    }
    
    .action-list-item {
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
    }
    
    .action-list-item-title {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .action-list-item-meta {
        color: var(--text-dim);
        font-size: 12px;
        line-height: 1.6;
    }
    
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin-left: 8px;
        font-family: 'IBM Plex Mono', monospace;
        text-transform: uppercase;
    }
    
    .status-badge.pending {
        background: var(--amber-dim);
        color: var(--amber);
    }
    
    .status-badge.in-progress {
        background: var(--cyan-dim);
        color: var(--cyan);
    }
    
    .status-badge.completed {
        background: var(--success-dim);
        color: var(--success);
    }
    
    /* Activity Log */
    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .activity-item {
        background: var(--bg);
        border-left: 3px solid var(--cyan);
        border-radius: 4px;
        padding: 16px;
        margin-bottom: 12px;
    }
    
    .activity-user {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
    }
    
    .activity-description {
        color: var(--text-dim);
        font-size: 14px;
        margin-top: 4px;
    }
    
    .activity-time {
        color: var(--text-faint);
        font-size: 12px;
        margin-top: 6px;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-faint);
        font-size: 14px;
    }
    
    /* VirusTotal Enhanced Display Styles */
    .virustotal-results {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .virustotal-scan-item {
        background: var(--bg);
        border: 2px solid var(--line-soft);
        border-radius: 12px;
        padding: 20px;
        transition: border-color 0.2s;
    }
    
    .virustotal-scan-item:hover {
        border-color: var(--cyan);
    }
    
    .vt-file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--line-soft);
    }
    
    .vt-file-info i {
        color: var(--cyan);
        font-size: 18px;
    }
    
    .vt-filename {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .vt-result {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .vt-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        font-family: 'IBM Plex Mono', monospace;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .vt-badge i {
        font-size: 16px;
    }
    
    .vt-badge.vt-danger {
        background: var(--rose-dim);
        color: var(--rose);
        border: 2px solid var(--rose);
    }
    
    .vt-badge.vt-success {
        background: var(--success-dim);
        color: var(--success);
        border: 2px solid var(--success);
    }
    
    .vt-badge.vt-unknown {
        background: var(--amber-dim);
        color: var(--amber);
        border: 2px solid var(--amber);
    }
    
    .vt-badge.vt-info {
        background: var(--cyan-dim);
        color: var(--cyan);
        border: 2px solid var(--cyan);
    }
    
    .vt-count {
        font-size: 18px;
        font-weight: 800;
    }
    
    .vt-label {
        font-size: 11px;
        opacity: 0.9;
    }
    
    .vt-severity-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .vt-severity-indicator i {
        font-size: 12px;
    }
    
    .vt-severity-indicator.vt-critical {
        background: rgba(220, 38, 38, 0.15);
        color: #DC2626;
        border: 1px solid rgba(220, 38, 38, 0.3);
    }
    
    .vt-severity-indicator.vt-medium {
        background: var(--amber-dim);
        color: var(--amber);
        border: 1px solid rgba(245, 185, 66, 0.3);
    }
    
    .vt-severity-indicator.vt-low {
        background: rgba(59, 130, 246, 0.15);
        color: #3B82F6;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    
    .vt-severity-indicator.vt-safe {
        background: var(--success-dim);
        color: var(--success);
        border: 1px solid rgba(61, 214, 140, 0.3);
    }
    
    .vt-severity-indicator.vt-neutral {
        background: rgba(148, 163, 184, 0.15);
        color: var(--text-dim);
        border: 1px solid var(--line-soft);
    }
    
    .vt-powered {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--line-soft);
        color: var(--text-faint);
        font-size: 11px;
        text-align: right;
        font-weight: 500;
    }
    
    .vt-powered strong {
        color: var(--cyan);
        font-weight: 700;
    }
    
    /* Empty state */
    .virustotal-empty {
        background: var(--bg);
        border: 2px dashed var(--line-soft);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
    }
    
    .vt-empty-icon {
        font-size: 48px;
        color: var(--text-faint);
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .vt-empty-icon .fa-spin {
        color: var(--cyan);
        opacity: 0.7;
    }
    
    .vt-empty-text strong {
        color: var(--text);
        font-size: 15px;
        font-weight: 700;
        display: block;
        margin-bottom: 8px;
    }
    
    .vt-empty-text p {
        color: var(--text-dim);
        font-size: 13px;
        margin: 0;
    }
    
    /* Details/summary styling */
    details summary {
        color: var(--text-dim);
        font-size: 13px;
        font-weight: 600;
        padding: 8px 12px;
        background: var(--bg);
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    details summary:hover {
        background: var(--line-soft);
        color: var(--text);
    }
    
    details[open] summary {
        margin-bottom: 12px;
        color: var(--cyan);
    }
</style>

<div class="review-container">
    <div class="page-header">
        <h1 class="page-title">Report Review #{{ $report->id }}</h1>
        <p class="page-subtitle">Analyze and assess submitted threat report</p>
    </div>

    {{-- Community Insights --}}
    @if(count($communityInsights) > 0)
    <div class="insights-alert">
        <div class="insights-alert-title">
            <i class="fas fa-exclamation-triangle"></i>
            Community Insights
        </div>
        @foreach($communityInsights as $insight)
        <p>This {{ $insight['type'] }} has been reported <strong>{{ $insight['times_reported'] }} times</strong> previously.</p>
        @endforeach
    </div>
    @endif

    {{-- Report Details (Read Only) --}}
    <div class="review-card">
        <h3 class="card-title">Report Details</h3>
        
        <div class="detail-row">
            <div class="detail-label">Submitted By:</div>
            <div class="detail-value">{{ $report->user->name }} ({{ $report->user->email }})</div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">Submitted:</div>
            <div class="detail-value">{{ $report->created_at->format('M d, Y H:i') }}</div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">Category:</div>
            <div class="detail-value">{{ $report->category->name }}</div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">Description:</div>
            <div class="description-box">{{ $report->description }}</div>
        </div>

        @if($report->verdict === 'escalated_externally' && !empty($report->escalation_note))
        <div class="detail-row">
            <div class="detail-label">Escalation Note:</div>
            <div class="description-box">{{ $report->escalation_note }}</div>
        </div>
        @endif
    </div>

    {{-- Attachments --}}
    @if($report->attachments->count() > 0)
    <div class="review-card">
        <h3 class="card-title">Attachments ({{ $report->attachments->count() }})</h3>
        @foreach($report->attachments as $attachment)
        <div class="attachment-item">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="flex: 1;">
                    <div class="attachment-filename">
                        @php
                            $extension = pathinfo($attachment->original_filename, PATHINFO_EXTENSION);
                            $icon = 'fa-file';
                            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                $icon = 'fa-file-image';
                            } elseif (in_array(strtolower($extension), ['pdf'])) {
                                $icon = 'fa-file-pdf';
                            } elseif (in_array(strtolower($extension), ['doc', 'docx'])) {
                                $icon = 'fa-file-word';
                            } elseif (in_array(strtolower($extension), ['zip', 'rar', '7z'])) {
                                $icon = 'fa-file-archive';
                            }
                            
                            // Get file size
                            $filePath = Storage::disk('local')->path($attachment->storage_path);
                            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                            $fileSizeKB = number_format($fileSize / 1024, 2);
                        @endphp
                        <i class="fas {{ $icon }}"></i> {{ $attachment->original_filename }}
                    </div>
                    <div class="attachment-meta">
                        Type: {{ strtoupper(pathinfo($attachment->original_filename, PATHINFO_EXTENSION)) }} | Size: {{ $fileSizeKB }} KB | Hash: {{ substr($attachment->file_hash, 0, 16) }}...
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('attachments.download', ['attachment' => $attachment->id, 'view' => 1]) }}" target="_blank" class="attachment-btn view-btn" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('attachments.download', $attachment->id) }}" class="attachment-btn download-btn" title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Editable Fields (Analyst Actions) --}}
    <form method="POST" action="{{ route('analyst.report-queue.update', $report->id) }}" class="assessment-form">
        @csrf
        @method('PUT')
        <h3 class="card-title">Analyst Assessment</h3>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Severity:</label>
                <select name="severity" class="form-select">
                    <option value="low" {{ $report->severity == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $report->severity == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $report->severity == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ $report->severity == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Status:</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="under_review" {{ $report->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ $report->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Verdict:</label>
                <select name="verdict" class="form-select">
                    <option value="pending" {{ $report->verdict == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed_threat" {{ $report->verdict == 'confirmed_threat' ? 'selected' : '' }}>Confirmed Threat</option>
                    <option value="false_positive" {{ $report->verdict == 'false_positive' ? 'selected' : '' }}>False Positive</option>
                    <option value="escalated_externally" {{ $report->verdict == 'escalated_externally' ? 'selected' : '' }}>Escalated Externally</option>
                </select>
            </div>
        </div>

        {{-- VirusTotal Scan Results - Enhanced Display --}}
        <div class="form-group full-width" style="margin-top: 20px;">
            <label class="form-label">
                <i class="fas fa-shield-virus" style="color: var(--cyan);"></i> 
                VirusTotal Scan Results (Auto-Verify)
            </label>
            
            @if(!empty($report->scan_result))
                {{-- Parse and display scan results with visual badges --}}
                <div class="virustotal-results">
                    @php
                        $scanLines = explode("\n", $report->scan_result);
                    @endphp
                    
                    @foreach($scanLines as $line)
                        @if(!empty(trim($line)))
                            @php
                                // Extract filename and result
                                preg_match('/\[(.*?)\]\s*(.*)/', $line, $matches);
                                $filename = $matches[1] ?? 'Unknown file';
                                $result = $matches[2] ?? $line;
                                
                                // Determine scan status
                                $isMalicious = false;
                                $isClean = false;
                                $isUnknown = false;
                                $detectionCount = 0;
                                $totalEngines = 0;
                                
                                if (preg_match('/(\d+)\/(\d+)\s+vendors?\s+flagged/', $result, $detectionMatches)) {
                                    $detectionCount = (int)$detectionMatches[1];
                                    $totalEngines = (int)$detectionMatches[2];
                                    $isMalicious = $detectionCount > 0;
                                    $isClean = $detectionCount === 0;
                                } elseif (stripos($result, 'No existing record') !== false) {
                                    $isUnknown = true;
                                }
                            @endphp
                            
                            <div class="virustotal-scan-item">
                                <div class="vt-file-info">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="vt-filename">{{ $filename }}</span>
                                </div>
                                
                                <div class="vt-result">
                                    @if($isMalicious)
                                        {{-- Malicious detection --}}
                                        <div class="vt-badge vt-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span class="vt-count">{{ $detectionCount }}/{{ $totalEngines }}</span>
                                            <span class="vt-label">DETECTIONS</span>
                                        </div>
                                        @if($detectionCount >= 10)
                                            <div class="vt-severity-indicator vt-critical">
                                                <i class="fas fa-radiation"></i> HIGH RISK
                                            </div>
                                        @elseif($detectionCount >= 3)
                                            <div class="vt-severity-indicator vt-medium">
                                                <i class="fas fa-exclamation-circle"></i> MODERATE RISK
                                            </div>
                                        @else
                                            <div class="vt-severity-indicator vt-low">
                                                <i class="fas fa-info-circle"></i> LOW RISK
                                            </div>
                                        @endif
                                    @elseif($isClean)
                                        {{-- Clean file --}}
                                        <div class="vt-badge vt-success">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="vt-count">{{ $detectionCount }}/{{ $totalEngines }}</span>
                                            <span class="vt-label">CLEAN</span>
                                        </div>
                                        <div class="vt-severity-indicator vt-safe">
                                            <i class="fas fa-shield-check"></i> NO THREATS DETECTED
                                        </div>
                                    @elseif($isUnknown)
                                        {{-- Unknown file --}}
                                        <div class="vt-badge vt-unknown">
                                            <i class="fas fa-question-circle"></i>
                                            <span class="vt-label">NOT IN DATABASE</span>
                                        </div>
                                        <div class="vt-severity-indicator vt-neutral">
                                            <i class="fas fa-search"></i> File not previously scanned
                                        </div>
                                    @else
                                        {{-- Other results --}}
                                        <div class="vt-badge vt-info">
                                            <i class="fas fa-info-circle"></i>
                                            <span class="vt-label">{{ $result }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="vt-powered">
                                    Powered by <strong>VirusTotal</strong>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                {{-- Optional: Manual override textarea (hidden by default, can expand) --}}
                <details style="margin-top: 12px;">
                    <summary style="cursor: pointer; color: var(--text-dim); font-size: 13px; font-weight: 600;">
                        <i class="fas fa-edit"></i> Edit Scan Result Manually
                    </summary>
                    <textarea name="scan_result" rows="3" class="form-textarea" style="margin-top: 8px;">{{ $report->scan_result }}</textarea>
                </details>
            @else
                {{-- No scan results yet - show status message --}}
                <div class="virustotal-empty">
                    @if($report->attachments->count() > 0)
                        <div class="vt-empty-icon">
                            <i class="fas fa-sync-alt fa-spin"></i>
                        </div>
                        <div class="vt-empty-text">
                            <strong>Scan in progress...</strong>
                            <p>VirusTotal analysis is being processed. Results will appear here automatically.</p>
                        </div>
                    @else
                        <div class="vt-empty-icon">
                            <i class="fas fa-file-slash"></i>
                        </div>
                        <div class="vt-empty-text">
                            <strong>No file attached</strong>
                            <p>This report contains no file attachments to scan.</p>
                        </div>
                    @endif
                </div>
                
                {{-- Hidden textarea for manual entry if needed --}}
                <textarea name="scan_result" rows="3" class="form-textarea" style="margin-top: 12px;" placeholder="Manual scan result (optional)">{{ $report->scan_result }}</textarea>
            @endif
        </div>

        <div class="form-group full-width" style="margin-top: 20px;">
            <label class="form-label">Escalation Note:</label>
            <textarea name="escalation_note" rows="3" class="form-textarea">{{ $report->escalation_note }}</textarea>
        </div>

        <button type="submit" class="btn-success" style="margin-top: 24px;">
            <i class="fas fa-save"></i> Update Report
        </button>
    </form>

    {{-- Action Assignment Section --}}
    <div class="action-card">
        <h3 class="card-title">Assign Action</h3>
        <form method="POST" action="{{ route('analyst.actions.store', $report->id) }}">
            @csrf
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Action Type:</label>
                <input type="text" name="action_type" required class="form-input" placeholder="e.g., Block URL, Quarantine File, Contact User">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Notes:</label>
                <textarea name="notes" rows="2" class="form-textarea" placeholder="Add any additional notes..."></textarea>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-user-check"></i> Assign Action to Myself
            </button>
        </form>

        {{-- Existing Actions --}}
        @if($report->actions->count() > 0)
        <h4 class="card-title" style="margin-top: 32px; margin-bottom: 16px;">Assigned Actions:</h4>
        @foreach($report->actions as $action)
        <div class="action-list-item">
            <div class="action-list-item-title">
                {{ $action->action_type }}
                <span class="status-badge {{ strtolower(str_replace(' ', '-', $action->status)) }}">
                    {{ ucfirst($action->status) }}
                </span>
            </div>
            <div class="action-list-item-meta">
                Assigned to: {{ $action->assignedUser->name ?? 'N/A' }} | Created: {{ $action->created_at->format('M d, Y') }}
                @if($action->notes)
                <br>Notes: {{ $action->notes }}
                @endif
            </div>
        </div>
        @endforeach
        @endif
    </div>

    {{-- Activity Log --}}
    <div class="review-card">
        <h3 class="card-title">Activity Log</h3>
        @if($report->activityLogs->count() > 0)
        <ul class="activity-list">
            @foreach($report->activityLogs as $log)
            <li class="activity-item">
                <div class="activity-user">{{ $log->user->name }}</div>
                <div class="activity-description">{{ $log->action_description }}</div>
                <div class="activity-time">{{ $log->created_at->format('M d, Y H:i') }}</div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list" style="font-size: 32px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
            No activity logged yet.
        </div>
        @endif
    </div>

    <div style="margin-top: 32px;">
        <a href="{{ route('analyst.report-queue.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Report Queue
        </a>
    </div>
</div>
@endsection
