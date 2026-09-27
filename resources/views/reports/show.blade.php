@extends('layouts.app')

@section('title', 'Report #'.$report->id.' - SentryDesk')

@section('page-context-title', 'Report #'.$report->id)

@section('ticker-content')
    <div class="ticker-item">
        STATUS: <span class="ticker-status-ok">{{ strtoupper(str_replace('_', ' ', $report->status)) }}</span>
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        SUBMITTED: {{ strtoupper($report->created_at->diffForHumans()) }}
    </div>
@endsection

@section('content')
<style>
    .report-container {
        width: 100%;
    }

    /* ── Hero banner — mirrors .hero-row, border-left color by status ── */
    .report-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        background: var(--bg-card);
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        position: relative;
        overflow: hidden;
    }

    [data-theme="light"] .report-hero {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .report-hero::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        pointer-events: none;
        opacity: 0.6;
    }

    .report-hero-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.4px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .report-hero-title i { font-size: 20px; }

    .report-hero-subtitle {
        font-size: 14px;
        color: var(--text-dim);
        font-weight: 400;
    }

    /* ── Edit button — mirrors hero-submit-btn but in amber ── */
    .edit-btn {
        background: var(--amber);
        color: #1A0F00;
        border: none;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
        z-index: 1;
        box-shadow: 0 4px 14px rgba(245,185,66,0.3);
    }

    .edit-btn:hover {
        background: #F5CC5A;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(245,185,66,0.4);
    }

    /* ── Dashboard cards — identical to .dashboard-card ── */
    .report-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        margin-bottom: 20px;
    }

    [data-theme="light"] .report-card {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    /* ── Card header — identical to .card-header ── */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line-soft);
    }

    /* ── Card title — identical to .card-title ── */
    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i { color: var(--cyan); font-size: 14px; }

    /* ── Detail rows ── */
    .detail-row {
        display: flex;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid var(--line-soft);
        gap: 16px;
    }

    .detail-row:last-child { border-bottom: none; }

    .detail-label {
        color: var(--text-dim);
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        min-width: 160px;
        flex-shrink: 0;
    }

    .detail-value {
        color: var(--text);
        font-size: 14px;
        flex: 1;
    }

    /* ── Badges — mirrors dashboard badge style, slightly larger ── */
    .status-badge,
    .severity-badge,
    .verdict-badge {
        font-family: 'IBM Plex Mono', monospace;
        padding: 5px 13px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .severity-badge::before,
    .verdict-badge::before {
        content: '●';
        font-size: 8px;
    }

    /* Status */
    .status-badge.pending       { background: var(--amber-dim);             color: var(--amber); }
    .status-badge.under_review  { background: var(--violet-dim);            color: var(--violet); }
    .status-badge.in_progress   { background: rgba(59,130,246,.15);         color: #3B82F6; }
    .status-badge.resolved      { background: var(--success-dim);           color: var(--success); }

    /* Severity */
    .severity-badge.critical    { background: var(--rose-dim);              color: var(--rose); }
    .severity-badge.high        { background: var(--amber-dim);             color: var(--amber); }
    .severity-badge.medium      { background: rgba(233,213,102,.15);        color: var(--medium); }
    .severity-badge.low         { background: var(--success-dim);           color: var(--success); }

    /* Verdict */
    .verdict-badge.pending              { background: rgba(148,163,184,.15); color: var(--text-dim); }
    .verdict-badge.confirmed_threat     { background: var(--rose-dim);       color: var(--rose); }
    .verdict-badge.false_positive       { background: var(--success-dim);    color: var(--success); }
    .verdict-badge.escalated_externally { background: var(--violet-dim);     color: var(--violet); }

    /* ── Description box ── */
    .description-box {
        background: var(--bg);
        border-radius: 10px;
        padding: 22px 24px;
        color: var(--text);
        font-size: 14px;
        line-height: 1.9;
        white-space: pre-wrap;
    }

    /* ── Attachment item — card-in-card style ── */
    .attachment-item {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--bg);
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 12px;
        transition: box-shadow 0.2s;
    }

    .attachment-item:last-child { margin-bottom: 0; }

    .attachment-item:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.25); }

    .attachment-icon-wrap {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--cyan-dim);
        color: var(--cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .attachment-filename {
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .attachment-meta {
        color: var(--text-faint);
        font-size: 12px;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ── Info box (pending/under-review user notice) ── */
    .info-box {
        background: var(--cyan-dim);
        border: 1px solid rgba(52,228,214,0.2);
        border-radius: 12px;
        padding: 18px 22px;
        color: var(--cyan);
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    [data-theme="light"] .info-box {
        border-color: rgba(8,145,178,0.3);
    }

    .info-box i { font-size: 20px; flex-shrink: 0; }

    /* ── Resolution note box ── */
    .note-box {
        background: var(--amber-dim);
        border: 1px solid rgba(245,185,66,0.25);
        border-radius: 12px;
        padding: 20px 24px;
        color: var(--amber);
        font-size: 14px;
        margin-bottom: 20px;
    }

    [data-theme="light"] .note-box {
        border-color: rgba(217,119,6,0.3);
    }

    .note-box-title {
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── Activity log items — mirrors .activity-item on dashboard ── */
    .activity-list { display: flex; flex-direction: column; gap: 12px; }

    .activity-log-item {
        display: flex;
        gap: 14px;
        padding: 16px 18px;
        background: rgba(255,255,255,0.03);
        border-radius: 10px;
        border-left: 3px solid var(--cyan);
        transition: background 0.2s;
    }

    [data-theme="light"] .activity-log-item {
        background: rgba(0,0,0,0.03);
    }

    .activity-log-item:hover { background: var(--cyan-dim); }

    .activity-log-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: var(--cyan-dim);
        color: var(--cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .activity-log-body { flex: 1; min-width: 0; }

    .activity-log-user {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .activity-log-desc {
        font-size: 13px;
        color: var(--text-dim);
        margin-bottom: 5px;
    }

    .activity-log-time {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* ── Empty state — mirrors dashboard ── */
    .empty-state {
        text-align: center;
        padding: 36px 20px;
        color: var(--text-faint);
        font-size: 13px;
    }

    .empty-state i {
        font-size: 38px;
        opacity: 0.25;
        margin-bottom: 12px;
        display: block;
    }

    /* ── Back button ── */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-card);
        color: var(--text-dim);
        border: 1.5px solid var(--line-soft);
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        margin-top: 8px;
    }

    [data-theme="light"] .back-btn { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

    .back-btn:hover {
        background: var(--line-soft);
        color: var(--text);
        border-color: var(--line);
    }
</style>

@php
    /* Determine hero accent colour by status */
    $statusAccent = match($report->status) {
        'resolved'     => 'var(--success)',
        'in_progress'  => '#3B82F6',
        'under_review' => 'var(--violet)',
        default        => 'var(--amber)',
    };
    $statusAccentDim = match($report->status) {
        'resolved'     => 'var(--success-dim)',
        'in_progress'  => 'rgba(59,130,246,.12)',
        'under_review' => 'var(--violet-dim)',
        default        => 'var(--amber-dim)',
    };
    $statusIcon = match($report->status) {
        'resolved'     => 'fa-check-circle',
        'in_progress'  => 'fa-sync-alt',
        'under_review' => 'fa-eye',
        default        => 'fa-clock',
    };
@endphp

<div class="report-container">

    {{-- Hero Banner --}}
    <div class="report-hero" style="border-left: 4px solid {{ $statusAccent }};">
        <style>
            .report-hero::after { background: {{ $statusAccentDim }}; }
        </style>
        <div>
            <div class="report-hero-title" style="color: var(--text);">
                <i class="fas {{ $statusIcon }}" style="color: {{ $statusAccent }};"></i>
                Threat Report #{{ $report->id }}
            </div>
            <div class="report-hero-subtitle">{{ $report->category->name }}</div>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isAnalyst())
        <a href="{{ route('reports.edit', $report->id) }}" class="edit-btn">
            <i class="fas fa-edit"></i> Edit Report
        </a>
        @endif
    </div>

    {{-- Status notice for regular users --}}
    @if(Auth::user()->isUser())
        @if(in_array($report->status, ['pending', 'under_review']))
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>Your report is being reviewed by our security team. You will be notified when there are updates.</div>
        </div>
        @endif
    @endif

    {{-- Resolution note --}}
    @if($report->escalation_note && $report->status == 'resolved')
    <div class="note-box">
        <div class="note-box-title"><i class="fas fa-clipboard-check"></i> Resolution Note</div>
        {{ $report->escalation_note }}
    </div>
    @endif

    {{-- 2-COLUMN GRID LAYOUT --}}
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        
        {{-- LEFT COLUMN: Report Details --}}
        <div class="report-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-file-shield"></i> Report Details</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Submitted By</div>
                <div class="detail-value">{{ $report->user->name }} <span style="color:var(--text-faint);font-size:13px;">({{ $report->user->email }})</span></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Category</div>
                <div class="detail-value">{{ $report->category->name }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Severity</div>
                <div class="detail-value">
                    <span class="severity-badge {{ $report->severity }}">
                        {{ strtoupper($report->severity) }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="status-badge {{ $report->status }}">
                        {{ str_replace('_', ' ', ucfirst($report->status)) }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Verdict</div>
                <div class="detail-value">
                    @if($report->verdict)
                        <span class="verdict-badge {{ $report->verdict }}">
                            {{ str_replace('_', ' ', $report->verdict) }}
                        </span>
                    @else
                        <span class="verdict-badge pending">Pending</span>
                    @endif
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Submitted</div>
                <div class="detail-value" style="font-family:'IBM Plex Mono',monospace;font-size:13px;color:var(--text-dim);">
                    {{ $report->created_at->format('M d, Y · H:i') }}
                </div>
            </div>

            @if($report->verdict === 'escalated_externally' && !empty($report->escalation_note))
            <div class="detail-row">
                <div class="detail-label">Escalation Note</div>
                <div class="detail-value">
                    <div class="description-box" style="margin-top: 0;">{{ $report->escalation_note }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT COLUMN: Attachments --}}
        @if($report->attachments->count() > 0)
        <div class="report-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-paperclip"></i> Attachments</div>
                <span style="font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--text-faint);font-weight:700;">
                    {{ $report->attachments->count() }} FILE{{ $report->attachments->count() > 1 ? 'S' : '' }}
                </span>
            </div>
            @foreach($report->attachments as $attachment)
            <div class="attachment-item">
                <div class="attachment-icon-wrap">
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
                    @endphp
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div style="flex: 1;">
                    <div class="attachment-filename">{{ $attachment->original_filename }}</div>
                    <div class="attachment-meta">{{ $attachment->file_type }} &nbsp;·&nbsp; {{ substr($attachment->file_hash, 0, 20) }}...</div>
                </div>
                <div style="display: flex; gap: 8px; margin-left: auto;">
                    <a href="{{ route('attachments.download', ['attachment' => $attachment->id, 'view' => 1]) }}" 
                       target="_blank" 
                       style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--cyan-dim); color: var(--cyan); text-decoration: none; transition: all 0.2s;"
                       title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('attachments.download', $attachment->id) }}" 
                       style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--success-dim); color: var(--success); text-decoration: none; transition: all 0.2s;"
                       title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Empty state for attachments --}}
        <div class="report-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-paperclip"></i> Attachments</div>
            </div>
            <div class="empty-state">
                <i class="fas fa-paperclip"></i>
                No attachments
            </div>
        </div>
        @endif

    </div>

    {{-- 2-COLUMN: Description and Scan Result --}}
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        
        {{-- LEFT: Description --}}
        <div class="report-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-align-left"></i> Description</div>
            </div>
            <div class="description-box">{{ $report->description }}</div>
        </div>

        {{-- RIGHT: Scan Result --}}
        @if($report->scan_result)
        <div class="report-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-microscope"></i> Scan Result</div>
            </div>
            <div class="description-box">{{ $report->scan_result }}</div>
        </div>
        @else
        <div class="report-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-microscope"></i> Scan Result</div>
            </div>
            <div class="empty-state">
                <i class="fas fa-microscope"></i>
                No scan results yet
            </div>
        </div>
        @endif

    </div>

    {{-- Comments Section --}}
    <div class="report-card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-comments"></i> Comments</div>
            @if($report->comments->count() > 0)
            <span style="font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--text-faint);font-weight:700;">
                {{ $report->comments->count() }} COMMENT{{ $report->comments->count() > 1 ? 'S' : '' }}
            </span>
            @endif
        </div>

        @if($report->comments->count() > 0)
        <div class="activity-list">
            @foreach($report->comments as $comment)
            <div class="activity-log-item">
                <div class="activity-log-icon"><i class="fas fa-user"></i></div>
                <div class="activity-log-body">
                    <div class="activity-log-user">{{ $comment->user->name }}</div>
                    <div class="activity-log-desc">{{ $comment->comment }}</div>
                    <div class="activity-log-time">{{ $comment->created_at->format('M d, Y · H:i') }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            No comments yet. Be the first to comment!
        </div>
        @endif

        {{-- Add Comment Form --}}
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--line-soft);">
            <form method="POST" action="{{ route('reports.comments.store', $report->id) }}">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text); margin-bottom: 7px;">Add Comment</label>
                    <textarea 
                        name="comment" 
                        rows="3" 
                        required 
                        maxlength="1000"
                        placeholder="Write a comment..."
                        style="width: 100%; padding: 11px 13px; background: var(--bg-input); color: var(--text); border: 1px solid var(--line); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; transition: border-color 0.18s ease, box-shadow 0.18s ease; resize: vertical;"
                    ></textarea>
                    @error('comment')
                        <span style="font-size: 11px; color: var(--rose); margin-top: 5px; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                <button type="submit" style="background: var(--cyan); color: #04211E; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;">
                    <i class="fas fa-paper-plane"></i> Post Comment
                </button>
            </form>
        </div>
    </div>

    <a href="{{ route('reports.index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>

    @if(Auth::user()->id === $report->user_id || Auth::user()->isAdmin() || Auth::user()->isAnalyst())
    <form method="POST" action="{{ route('reports.destroy', $report->id) }}" style="display: inline-block; margin-left: 12px;" onsubmit="return confirm('Are you sure you want to remove this report? It will be archived and can be restored by admins.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="back-btn" style="background: var(--rose-dim); color: var(--rose); border-color: var(--rose);">
            <i class="fas fa-archive"></i> Remove Report
        </button>
    </form>
    @endif

</div>
@endsection
