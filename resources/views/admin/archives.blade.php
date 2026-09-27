@extends('layouts.app')

@section('title', 'Archived Reports - SentryDesk')

@section('page-context-title', 'Archived Reports')

@section('content')
<style>
    .archives-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 28px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
        letter-spacing: -0.4px;
    }

    .page-subtitle {
        color: var(--text-dim);
        font-size: 14px;
    }

    .archives-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }

    [data-theme="light"] .archives-card {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .archives-table {
        width: 100%;
        border-collapse: collapse;
    }

    .archives-table thead th {
        text-align: left;
        padding: 14px 16px;
        background: var(--bg);
        color: var(--text-dim);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 2px solid var(--line-soft);
    }

    .archives-table tbody td {
        padding: 18px 16px;
        color: var(--text);
        font-size: 14px;
        border-bottom: 1px solid var(--line-soft);
    }

    .archives-table tbody tr {
        transition: background 0.2s;
    }

    .archives-table tbody tr:hover {
        background: var(--bg);
    }

    .report-id {
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 700;
        color: var(--cyan);
        font-size: 15px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--cyan-dim);
        border: 2px solid var(--cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: var(--cyan);
        flex-shrink: 0;
        overflow: hidden;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-name {
        font-weight: 600;
        color: var(--text);
    }

    .archive-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        background: var(--amber-dim);
        color: var(--amber);
        font-family: 'IBM Plex Mono', monospace;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .archive-meta {
        font-size: 12px;
        color: var(--text-faint);
        margin-top: 4px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .action-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-restore {
        background: var(--success-dim);
        color: var(--success);
    }

    .btn-restore:hover {
        background: rgba(61,214,140,0.22);
    }

    .btn-delete {
        background: var(--rose-dim);
        color: var(--rose);
    }

    .btn-delete:hover {
        background: rgba(255,92,122,0.22);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-faint);
    }

    .empty-state i {
        font-size: 56px;
        opacity: 0.2;
        margin-bottom: 16px;
        display: block;
    }

    .empty-state-text {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .empty-state-hint {
        font-size: 13px;
        color: var(--text-dim);
    }

    /* Pagination */
    .pagination-container {
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        color: var(--text-dim);
        font-size: 13px;
        font-family: 'IBM Plex Mono', monospace;
    }

    .pagination-links {
        display: flex;
        gap: 8px;
    }

    .page-link {
        padding: 8px 14px;
        border-radius: 8px;
        background: var(--bg);
        color: var(--text);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid var(--line-soft);
    }

    .page-link:hover {
        background: var(--line-soft);
    }

    .page-link.active {
        background: var(--cyan);
        color: #04211E;
        border-color: var(--cyan);
    }

    .page-link.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
</style>

<div class="archives-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-archive" style="color: var(--amber);"></i> 
            Archived Reports
        </h1>
        <p class="page-subtitle">View and manage archived threat reports. Restore or permanently delete archived reports.</p>
    </div>

    <div class="archives-card">
        @if($archivedReports->count() > 0)
            <table class="archives-table">
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Submitted By</th>
                        <th>Category</th>
                        <th>Archived By</th>
                        <th>Archived Date</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archivedReports as $report)
                    <tr>
                        <td>
                            <span class="report-id">#{{ $report->id }}</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    @if($report->user->profile_picture)
                                        <img src="{{ asset('storage/' . $report->user->profile_picture) }}" alt="{{ $report->user->name }}">
                                    @else
                                        {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="user-name">{{ $report->user->name }}</div>
                                    <div style="font-size: 12px; color: var(--text-faint);">{{ $report->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $report->category->name }}</td>
                        <td>
                            <div class="archive-badge">
                                <i class="fas fa-user"></i>
                                {{ $report->archived_by ?? 'Unknown' }}
                            </div>
                            @if($report->archive_reason)
                            <div class="archive-meta">{{ $report->archive_reason }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-family: 'IBM Plex Mono', monospace; font-size: 13px;">
                                {{ $report->deleted_at->format('M d, Y') }}
                            </div>
                            <div style="font-size: 11px; color: var(--text-faint); font-family: 'IBM Plex Mono', monospace;">
                                {{ $report->deleted_at->format('H:i') }} ({{ $report->deleted_at->diffForHumans() }})
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <form method="POST" action="{{ route('admin.archives.restore', $report->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="action-btn btn-restore" onclick="return confirm('Restore this report? It will be visible again in the reports list.');">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.archives.force-delete', $report->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" onclick="return confirm('⚠️ PERMANENTLY DELETE this report? This action CANNOT be undone and all associated files will be deleted!');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($archivedReports->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $archivedReports->firstItem() }} to {{ $archivedReports->lastItem() }} of {{ $archivedReports->total() }} archived reports
                </div>
                <div class="pagination-links">
                    @if($archivedReports->onFirstPage())
                        <span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $archivedReports->previousPageUrl() }}" class="page-link"><i class="fas fa-chevron-left"></i></a>
                    @endif

                    @foreach(range(1, $archivedReports->lastPage()) as $page)
                        @if($page == $archivedReports->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $archivedReports->url($page) }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($archivedReports->hasMorePages())
                        <a href="{{ $archivedReports->nextPageUrl() }}" class="page-link"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-archive"></i>
                <div class="empty-state-text">No Archived Reports</div>
                <div class="empty-state-hint">Archived reports will appear here</div>
            </div>
        @endif
    </div>
</div>
@endsection
