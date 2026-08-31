@extends('layouts.app')

@section('title', 'My Reports - SentryDesk')

@section('page-context-title', 'My Reports')

@section('ticker-content')
    <div class="ticker-item">
        {{ $reports->total() }} TOTAL REPORTS
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        LAST UPDATED: {{ $reports->count() > 0 ? strtoupper($reports->first()->created_at->diffForHumans()) : 'NONE' }}
    </div>
@endsection

@section('content')
<style>
    .reports-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    
    .page-title-section h1 {
        font-size: 30px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    
    .page-title-section p {
        color: var(--text-dim);
        font-size: 14px;
        font-weight: 400;
    }
    
    .submit-btn {
        background: var(--cyan);
        color: #04211E;
        border: none;
        padding: 12px 24px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    
    .submit-btn:hover {
        background: #5AEEE3;
    }
    
    .table-card {
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .table-card {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    .reports-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .reports-table thead th {
        background: var(--bg);
        color: var(--text-dim);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        padding: 16px 20px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .reports-table thead th {
        border-bottom: 1px solid var(--line);
    }
    
    .reports-table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.2s;
    }
    
    [data-theme="light"] .reports-table tbody tr {
        border-bottom: 1px solid var(--line);
    }
    
    .reports-table tbody tr:hover {
        background: var(--cyan-dim);
    }
    
    .reports-table tbody tr:last-child {
        border-bottom: none;
    }
    
    .reports-table tbody td {
        padding: 18px 20px;
        color: var(--text);
        font-size: 14px;
    }
    
    .report-id {
        font-family: 'IBM Plex Mono', monospace;
        color: var(--cyan);
        font-weight: 600;
    }
    
    .status-badge,
    .severity-badge,
    .verdict-badge {
        font-family: 'Inter', sans-serif;
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    
    .verdict-badge::before,
    .severity-badge::before {
        content: '● ';
    }
    
    /* Status Badge Colors */
    .status-badge.pending {
        background: rgba(148, 163, 184, 0.15);
        color: var(--text-dim);
    }
    
    .status-badge.under_review {
        background: rgba(249, 115, 22, 0.15);
        color: #F97316;
    }
    
    .status-badge.in_progress {
        background: var(--cyan-dim);
        color: var(--cyan);
    }
    
    .status-badge.resolved {
        background: rgba(34, 197, 94, 0.15);
        color: #22C55E;
    }
    
    /* Severity Badge Colors */
    .severity-badge.critical {
        background: var(--rose-dim);
        color: var(--rose);
    }
    
    .severity-badge.high {
        background: var(--amber-dim);
        color: var(--amber);
    }
    
    .severity-badge.medium {
        background: rgba(233, 213, 102, 0.15);
        color: var(--medium);
    }
    
    .severity-badge.low {
        background: var(--success-dim);
        color: var(--success);
    }
    
    /* Verdict Badge Colors */
    .verdict-badge.pending {
        background: rgba(148, 163, 184, 0.15);
        color: var(--text-dim);
    }
    
    .verdict-badge.confirmed_threat {
        background: var(--rose-dim);
        color: var(--rose);
    }
    
    .verdict-badge.false_positive {
        background: var(--success-dim);
        color: var(--success);
    }
    
    .verdict-badge.escalated_externally {
        background: var(--violet-dim);
        color: var(--violet);
    }
    
    .view-link {
        background: var(--cyan-dim);
        color: var(--cyan);
        padding: 6px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        transition: all 0.2s;
        display: inline-block;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .view-link:hover {
        background: rgba(52, 228, 214, 0.18);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--text-faint);
    }
    
    .empty-state i {
        font-size: 64px;
        opacity: 0.3;
        margin-bottom: 20px;
        display: block;
        color: var(--text-faint);
    }
    
    .empty-state-text {
        font-size: 16px;
        margin-bottom: 24px;
        color: var(--text-dim);
    }
    
    .empty-state-link {
        background: var(--cyan);
        color: #04211E;
        border: none;
        padding: 14px 28px;
        border-radius: 9px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    
    .empty-state-link:hover {
        background: #5AEEE3;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 24px;
    }
</style>

<div class="reports-container">
    <div class="page-header">
        <div class="page-title-section">
            <h1>My Reports</h1>
            <p>View and track all your submitted threat reports</p>
        </div>
        @if(Auth::user()->isUser())
        <a href="{{ route('reports.create') }}" class="submit-btn">
            <i class="fas fa-plus-circle"></i> Submit New Report
        </a>
        @endif
    </div>

    @if($reports->count() > 0)
    <div class="table-card">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>ID</th>
                    @if(Auth::user()->isAdmin() || Auth::user()->isAnalyst())
                    <th>Submitted By</th>
                    @endif
                    <th>Category</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Verdict</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr>
                    <td>
                        <span class="report-id">#{{ $report->id }}</span>
                    </td>
                    @if(Auth::user()->isAdmin() || Auth::user()->isAnalyst())
                    <td>{{ $report->user->name }}</td>
                    @endif
                    <td>{{ $report->category->name }}</td>
                    <td>
                        <span class="severity-badge {{ $report->severity }}">
                            {{ strtoupper($report->severity) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge {{ $report->status }}">
                            {{ str_replace('_', ' ', ucfirst($report->status)) }}
                        </span>
                    </td>
                    <td>
                        @if($report->verdict)
                            <span class="verdict-badge {{ $report->verdict }}">
                                {{ str_replace('_', ' ', $report->verdict) }}
                            </span>
                        @else
                            <span class="verdict-badge pending">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td style="color: var(--text-dim); font-family: 'IBM Plex Mono', monospace;">{{ $report->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('reports.show', $report->id) }}" class="view-link">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $reports->links() }}
    </div>
    @else
    <div class="table-card">
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <div class="empty-state-text">No reports found. Submit your first report to get started.</div>
            <a href="{{ route('reports.create') }}" class="empty-state-link">
                <i class="fas fa-plus-circle"></i> Submit Your First Report
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
