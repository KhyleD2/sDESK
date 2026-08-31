@extends('layouts.app')

@section('title', 'Activity Logs - SentryDesk')

@section('content')
<style>
    .activity-logs-container {
        padding: 0;
        max-width: 1800px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 16px;
    }
    
    .page-header h1 {
        font-size: 30px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    
    .page-header p {
        color: var(--text-dim);
        font-size: 14px;
        font-weight: 400;
    }
    
    .filter-bar {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .filter-bar {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr) auto auto;
        gap: 16px;
        align-items: end;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .filter-label {
        color: var(--text-dim);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .filter-input {
        background: var(--bg);
        border: 1px solid var(--line);
        color: var(--text);
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 400;
        width: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    [data-theme="light"] .filter-input {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .filter-input::placeholder {
        color: var(--text-faint);
    }
    
    .filter-input:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
    }
    
    .filter-input:hover {
        border-color: var(--line-soft);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    [data-theme="light"] .filter-input:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .filter-btn {
        background: var(--cyan);
        color: var(--bg);
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
    }
    
    .filter-btn:hover {
        opacity: 0.9;
    }
    
    .reset-btn {
        background: var(--line-soft);
        color: var(--text);
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
        text-decoration: none;
    }
    
    .reset-btn:hover {
        opacity: 0.8;
    }
    
    .table-card {
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .table-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .activity-table thead {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .activity-table thead {
        border-bottom: 1px solid var(--line);
    }
    
    .activity-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-dim);
        background: var(--bg);
    }
    
    .activity-table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.2s;
    }
    
    [data-theme="light"] .activity-table tbody tr {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .activity-table tbody tr:hover {
        background: var(--cyan-dim);
    }
    
    .activity-table tbody td {
        padding: 18px 20px;
        color: var(--text);
        font-size: 14px;
    }
    
    .timestamp-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .timestamp-date {
        color: var(--text);
        font-weight: 500;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .timestamp-time {
        color: var(--text-faint);
        font-size: 12px;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bg);
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .user-name {
        font-weight: 500;
        color: var(--text);
    }
    
    .report-link {
        color: var(--cyan);
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    
    .report-link:hover {
        opacity: 0.8;
    }
    
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .pagination-container {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .pagination-info {
        color: var(--text-dim);
        font-size: 14px;
        font-weight: 500;
    }
    
    .pagination-controls {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .page-btn {
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        background: var(--bg);
        border-radius: 6px;
        color: var(--text);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    [data-theme="light"] .page-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .page-btn:hover {
        background: var(--line-soft);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    [data-theme="light"] .page-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .page-btn.active {
        background: var(--cyan);
        color: var(--bg);
    }
    
    .page-btn.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    
    .page-btn.disabled:hover {
        background: var(--bg);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    [data-theme="light"] .page-btn.disabled:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .empty-state {
        padding: 80px 40px;
        text-align: center;
        color: var(--text-faint);
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .empty-state {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.3;
    }
</style>

<div class="activity-logs-container">
    <div class="page-header">
        <h1>Activity Logs</h1>
        <p>System-wide activity feed and audit trail</p>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar">
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Report ID</label>
                <input type="number" name="report_id" value="{{ request('report_id') }}" class="filter-input" placeholder="Filter by Report ID">
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
            </div>
            
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <a href="{{ Auth::user()->isAdmin() ? route('admin.activity-logs') : route('analyst.activity-logs') }}" class="reset-btn">
                <i class="fas fa-redo-alt"></i> Reset
            </a>
        </div>
    </form>

    <!-- Activity Logs Table -->
    @if($logs->count() > 0)
    <div class="table-card">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action Description</th>
                    <th>Report ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>
                        <div class="timestamp-cell">
                            <div class="timestamp-date">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="timestamp-time">{{ $log->created_at->format('h:i:s A') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">{{ strtoupper(substr($log->user->name, 0, 1)) }}</div>
                            <span class="user-name">{{ $log->user->name }}</span>
                        </div>
                    </td>
                    <td style="color: var(--text-dim);">{{ $log->action_description }}</td>
                    <td>
                        @if($log->report_id)
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.report-queue.show', $log->report_id) : route('analyst.report-queue.show', $log->report_id) }}" class="report-link">
                            #{{ $log->report_id }}
                        </a>
                        @else
                        <span style="color: var(--text-faint);">N/A</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
        </div>
        <div class="pagination-controls">
            @if ($logs->onFirstPage())
                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                @if ($page == $logs->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if ($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-history"></i>
        <p>No activity logs found matching your filters.</p>
        <a href="{{ Auth::user()->isAdmin() ? route('admin.activity-logs') : route('analyst.activity-logs') }}" style="color: #3B82F6; text-decoration: none; font-weight: 600; margin-top: 8px; display: inline-block;">Clear filters</a>
    </div>
    @endif
</div>
@endsection
