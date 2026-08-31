@extends('layouts.app')

@section('title', 'Report Queue - SentryDesk')

@section('content')
<style>
    /* Page Layout */
    .report-queue-container {
        padding: 0;
        max-width: 1800px;
        margin: 0 auto;
    }
    
    /* Page Header */
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
    
    .page-actions {
        display: flex;
        gap: 10px;
    }
    
    .icon-action-btn {
        width: 40px;
        height: 40px;
        background: var(--bg-card);
        border-radius: 8px;
        color: var(--text-dim);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: none;
    }
    
    [data-theme="light"] .icon-action-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    
    .icon-action-btn:hover {
        background: var(--line-soft);
        color: var(--text);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    [data-theme="light"] .icon-action-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    /* Filter Bar */
    .filter-bar {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr) auto auto;
        gap: 20px;
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
    
    .filter-select {
        background: var(--bg);
        color: var(--text);
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%2394A3B8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: box-shadow 0.2s, border-color 0.2s;
        border: 1px solid var(--line);
    }
    
    [data-theme="light"] .filter-select {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .filter-select:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border-color: var(--line-soft);
    }
    
    [data-theme="light"] .filter-select:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .filter-select:focus {
        outline: none;
        box-shadow: 0 0 0 3px var(--cyan-dim);
        border-color: var(--cyan);
    }
    
    .filter-btn {
        background: var(--success);
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
        background: var(--bg);
        color: var(--text);
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--line);
    }
    
    [data-theme="light"] .reset-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .reset-btn:hover {
        background: var(--line-soft);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    [data-theme="light"] .reset-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    /* Table Card */
    .table-card {
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .report-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .report-table thead {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .report-table thead {
        border-bottom: 1px solid var(--line);
    }
    
    .report-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-dim);
        background: var(--bg);
    }
    
    .report-table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.2s;
    }
    
    [data-theme="light"] .report-table tbody tr {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .report-table tbody tr:hover {
        background: var(--cyan-dim);
    }
    
    .report-table tbody td {
        padding: 18px 20px;
        color: var(--text);
        font-size: 14px;
    }
    
    /* Submitted By Column */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #10B981;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .user-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .user-name {
        font-weight: 600;
        color: var(--text);
        font-size: 14px;
    }
    
    .user-email {
        font-size: 12px;
        color: var(--text-faint);
    }
    
    /* Category Column */
    .category-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .category-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .category-dot.phishing { background: #3B82F6; }
    .category-dot.malware { background: #8B5CF6; }
    .category-dot.suspicious { background: #14B8A6; }
    .category-dot.social { background: #F59E0B; }
    .category-dot.data { background: #EF4444; }
    
    /* Severity Badge */
    .severity-badge {
        display: inline-flex;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .severity-badge.critical {
        background: #DC2626;
        color: #FFFFFF;
    }
    
    .severity-badge.high {
        background: #F97316;
        color: #FFFFFF;
    }
    
    .severity-badge.medium {
        background: #EAB308;
        color: #FFFFFF;
    }
    
    .severity-badge.low {
        background: #16A34A;
        color: #FFFFFF;
    }
    
    /* Status Column */
    .status-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        font-size: 14px;
    }
    
    .status-cell.resolved {
        color: #22C55E;
    }
    
    .status-cell.pending {
        color: #EAB308;
    }
    
    .status-cell.in-progress {
        color: #38BDF8;
    }
    
    .status-cell.under-review {
        color: #A78BFA;
    }
    
    /* Date Column */
    .date-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .date-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .date-main {
        font-size: 14px;
        color: var(--text);
    }
    
    .date-time {
        font-size: 12px;
        color: var(--text-faint);
    }
    
    /* Actions Column */
    .actions-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .view-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--bg);
        border-radius: 6px;
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--line);
    }
    
    [data-theme="light"] .view-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .view-btn:hover {
        background: var(--line-soft);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    [data-theme="light"] .view-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .more-btn {
        width: 32px;
        height: 32px;
        background: var(--bg);
        border-radius: 6px;
        color: var(--text-dim);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--line);
    }
    
    [data-theme="light"] .more-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .more-btn:hover {
        background: var(--line-soft);
        color: var(--text);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    [data-theme="light"] .more-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
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
        border-color: var(--cyan);
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
    
    /* Empty State */
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
    
    .empty-state p {
        font-size: 16px;
        margin-bottom: 8px;
    }
</style>

<div class="report-queue-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Report Queue</h1>
            <p>All threat reports across the system</p>
        </div>
        <div class="page-actions">
            <button class="icon-action-btn" title="Filter"><i class="fas fa-filter"></i></button>
            <button class="icon-action-btn" title="Download"><i class="fas fa-download"></i></button>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar">
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Severity</label>
                <select name="severity" class="filter-select">
                    <option value="">All Severities</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Sort By</label>
                <select name="sort" class="filter-select">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date (Newest)</option>
                    <option value="severity" {{ request('sort') == 'severity' ? 'selected' : '' }}>Severity (Critical First)</option>
                </select>
            </div>

            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Filter
            </button>

            <a href="{{ Auth::user()->isAdmin() ? route('admin.report-queue.index') : route('analyst.report-queue.index') }}" class="reset-btn">
                <i class="fas fa-redo-alt"></i> Reset
            </a>
        </div>
    </form>

    <!-- Table Card -->
    @if($reports->count() > 0)
    <div class="table-card">
        <table class="report-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Submitted By</th>
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
                    <td><strong>#{{ $report->id }}</strong></td>
                    
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">
                                {{ strtoupper(substr($report->user->name, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ $report->user->name }}</div>
                                <div class="user-email">{{ $report->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td>
                        <div class="category-cell">
                            <span class="category-dot {{ strtolower(str_replace([' ', '/'], '-', $report->category->name)) }}"></span>
                            <span>{{ $report->category->name }}</span>
                        </div>
                    </td>
                    
                    <td>
                        <span class="severity-badge {{ $report->severity }}">
                            {{ ucfirst($report->severity) }}
                        </span>
                    </td>
                    
                    <td>
                        <div class="status-cell {{ str_replace('_', '-', $report->status) }}">
                            @if($report->status == 'resolved')
                                <i class="fas fa-check-circle"></i>
                            @elseif($report->status == 'pending')
                                <i class="fas fa-clock"></i>
                            @elseif($report->status == 'in_progress')
                                <i class="fas fa-sync-alt"></i>
                            @else
                                <i class="fas fa-eye"></i>
                            @endif
                            {{ ucwords(str_replace('_', ' ', $report->status)) }}
                        </div>
                    </td>
                    
                    <td style="color: var(--text-dim);">
                        {{ ucwords(str_replace('_', ' ', $report->verdict)) }}
                    </td>
                    
                    <td>
                        <div class="date-cell">
                            <i class="fas fa-calendar" style="color: var(--text-faint);"></i>
                            <div class="date-info">
                                <div class="date-main">{{ $report->created_at->format('M d, Y') }}</div>
                                <div class="date-time">{{ $report->created_at->format('h:i A') }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td>
                        <div class="actions-cell">
                            <a href="{{ Auth::user()->isAdmin() ? route('admin.report-queue.show', $report->id) : route('analyst.report-queue.show', $report->id) }}" class="view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <button class="more-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} reports
        </div>
        <div class="pagination-controls">
            @if ($reports->onFirstPage())
                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $reports->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                @if ($page == $reports->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if ($reports->hasMorePages())
                <a href="{{ $reports->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>No reports found matching your filters.</p>
        <a href="{{ Auth::user()->isAdmin() ? route('admin.report-queue.index') : route('analyst.report-queue.index') }}" style="color: #22C55E; text-decoration: none; font-weight: 600;">Clear all filters</a>
    </div>
    @endif
</div>
@endsection
