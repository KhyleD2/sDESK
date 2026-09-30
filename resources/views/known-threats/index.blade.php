@extends('layouts.app')

@section('title', 'Known Threats - SentryDesk')

@section('content')
<style>
    .known-threats-container {
        padding: 0;
        max-width: 1800px;
        margin: 0 auto;
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
        grid-template-columns: 2fr 1fr auto auto;
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
    
    .filter-select {
        background: var(--bg);
        border: 1px solid var(--line);
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
        width: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    [data-theme="light"] .filter-select {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .filter-select:hover, .filter-input:hover {
        border-color: var(--line-soft);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    [data-theme="light"] .filter-select:hover, [data-theme="light"] .filter-input:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .search-btn {
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
    
    .search-btn:hover {
        opacity: 0.9;
    }
    
    .reset-btn {
        background: var(--bg);
        color: var(--text);
        border: 1px solid var(--line);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    [data-theme="light"] .reset-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .reset-btn:hover {
        background: var(--line-soft);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .reset-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .add-threat-card {
        background: var(--amber-dim);
        border: 1px solid var(--amber);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
    }
    
    .add-threat-card h3 {
        color: var(--text);
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 16px;
    }
    
    .add-threat-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
    }
    
    .add-btn {
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
    
    .add-btn:hover {
        opacity: 0.9;
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
    
    .threats-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .threats-table thead {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .threats-table thead {
        border-bottom: 1px solid var(--line);
    }
    
    .threats-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-dim);
        background: var(--bg);
    }
    
    .threats-table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.2s;
    }
    
    [data-theme="light"] .threats-table tbody tr {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .threats-table tbody tr:hover {
        background: var(--cyan-dim);
    }
    
    .threats-table tbody td {
        padding: 18px 20px;
        color: var(--text);
        font-size: 14px;
    }
    
    .indicator-cell {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        color: var(--text-dim);
        background: var(--bg);
        padding: 8px 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: space-between;
        max-width: 500px;
    }
    
    .copy-indicator-btn {
        background: var(--cyan-dim);
        color: var(--cyan);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    
    .copy-indicator-btn:hover {
        background: rgba(52, 228, 214, 0.18);
    }
    
    .type-badge {
        display: inline-flex;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .type-badge.file_hash {
        background: #8B5CF6;
        color: #FFFFFF;
    }
    
    .type-badge.url {
        background: #3B82F6;
        color: #FFFFFF;
    }
    
    .type-badge.ip {
        background: #14B8A6;
        color: #FFFFFF;
    }
    
    .type-badge.email {
        background: #F59E0B;
        color: #FFFFFF;
    }
    
    .type-badge.domain {
        background: #EC4899;
        color: #FFFFFF;
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
    
    .remove-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--rose-dim);
        border: 1px solid var(--rose);
        border-radius: 6px;
        color: var(--rose);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .remove-btn:hover {
        opacity: 0.9;
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

<div class="known-threats-container">
    <div class="page-header">
        <div class="page-title-section">
            <h1>Known Threats</h1>
            <p>Internal blocklist of confirmed threat indicators</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar">
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Search Indicator</label>
                <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Search by hash, URL, IP, email, or domain...">
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Type</label>
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="file_hash" {{ request('type') == 'file_hash' ? 'selected' : '' }}>File Hash</option>
                    <option value="url" {{ request('type') == 'url' ? 'selected' : '' }}>URL</option>
                    <option value="ip" {{ request('type') == 'ip' ? 'selected' : '' }}>IP Address</option>
                    <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="domain" {{ request('type') == 'domain' ? 'selected' : '' }}>Domain</option>
                </select>
            </div>
            
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Search
            </button>
            
            <a href="{{ Auth::user()->isAdmin() ? route('admin.known-threats') : route('analyst.known-threats') }}" class="reset-btn">
                <i class="fas fa-redo-alt"></i> Reset
            </a>
        </div>
    </form>

    <!-- Add New Threat (Admin Only) -->
    @if(Auth::user()->isAdmin())
    <div class="add-threat-card">
        <h3><i class="fas fa-plus-circle"></i> Add New Known Threat</h3>
        <form method="POST" action="{{ route('admin.known-threats.store') }}">
            @csrf
            <div class="add-threat-grid">
                <input type="text" name="indicator" required class="filter-input" placeholder="Indicator (hash, URL, IP, email, or domain)">
                
                <select name="type" required class="filter-select">
                    <option value="">Select Type</option>
                    <option value="file_hash">File Hash</option>
                    <option value="url">URL</option>
                    <option value="ip">IP Address</option>
                    <option value="email">Email</option>
                    <option value="domain">Domain</option>
                </select>
                
                <input type="number" name="first_reported_report_id" class="filter-input" placeholder="Report ID (optional)">
                
                <button type="submit" class="add-btn">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Threats Table -->
    @if($threats->count() > 0)
    <div class="table-card">
        <table class="threats-table">
            <thead>
                <tr>
                    <th>Indicator</th>
                    <th>Type</th>
                    <th>Times Reported</th>
                    <th>First Report</th>
                    <th>Added By</th>
                    <th>Date Added</th>
                    @if(Auth::user()->isAdmin())
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($threats as $threat)
                <tr>
                    <td>
                        <div class="indicator-cell" title="{{ $threat->indicator }}">
                            @if(strlen($threat->indicator) > 50)
                                <span style="font-family: 'IBM Plex Mono', monospace; font-size: 11px;">
                                    {{ substr($threat->indicator, 0, 24) }}<span style="color: var(--text-faint);">...</span>{{ substr($threat->indicator, -24) }}
                                </span>
                                <button onclick="copyToClipboard('{{ $threat->indicator }}')" class="copy-indicator-btn" title="Copy full indicator">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @else
                                {{ $threat->indicator }}
                            @endif
                        </div>
                        @if($threat->type === 'file_hash')
                            @php
                                $attachment = $threat->attachment();
                            @endphp
                            @if($attachment && \Storage::disk('local')->exists('threat_attachments/' . $attachment->stored_filename))
                                <a href="{{ route('attachments.download', $attachment->id) }}" class="view-file-btn" style="margin-top: 8px; display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--cyan-dim); border: 1px solid var(--cyan); border-radius: 6px; color: var(--cyan); font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;">
                                    <i class="fas fa-download"></i> Download File: {{ $attachment->original_filename }}
                                </a>
                            @elseif($attachment)
                                <div style="margin-top: 8px; padding: 6px 12px; background: var(--line-soft); border-radius: 6px; color: var(--text-dim); font-size: 11px;">
                                    <i class="fas fa-info-circle"></i> Original file: {{ $attachment->original_filename }} (no longer available)
                                </div>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($threat->type === 'file_hash')
                            @php
                                $attachment = $threat->attachment();
                                $fileType = 'File Hash';
                                if ($attachment) {
                                    // Get friendly file type from mime type
                                    $mime = $attachment->file_type;
                                    if (str_contains($mime, 'pdf')) {
                                        $fileType = 'PDF Document';
                                    } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
                                        $fileType = 'Word Document';
                                    } elseif (str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel')) {
                                        $fileType = 'Spreadsheet';
                                    } elseif (str_contains($mime, 'image')) {
                                        $fileType = 'Image File';
                                    } elseif (str_contains($mime, 'zip') || str_contains($mime, 'compressed')) {
                                        $fileType = 'Archive File';
                                    } elseif (str_contains($mime, 'text')) {
                                        $fileType = 'Text File';
                                    } elseif (str_contains($mime, 'executable') || str_contains($mime, 'application/x-msdownload')) {
                                        $fileType = 'Executable';
                                    } else {
                                        $fileType = 'File (' . pathinfo($attachment->original_filename, PATHINFO_EXTENSION) . ')';
                                    }
                                }
                            @endphp
                            <span class="type-badge file_hash">
                                {{ $fileType }}
                            </span>
                        @else
                            <span class="type-badge {{ $threat->type }}">
                                {{ ucfirst(str_replace('_', ' ', $threat->type)) }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong style="color: var(--text); font-size: 16px;">{{ $threat->times_reported }}</strong>
                    </td>
                    <td>
                        @if($threat->first_reported_report_id)
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.report-queue.show', $threat->first_reported_report_id) : route('analyst.report-queue.show', $threat->first_reported_report_id) }}" class="report-link">
                            #{{ $threat->first_reported_report_id }}
                        </a>
                        @else
                        <span style="color: var(--text-faint);">N/A</span>
                        @endif
                    </td>
                    <td style="color: var(--text-dim);">{{ $threat->addedByUser->name }}</td>
                    <td style="color: var(--text-dim);">{{ \Carbon\Carbon::parse($threat->added_at)->format('M d, Y') }}</td>
                    @if(Auth::user()->isAdmin())
                    <td>
                        <form method="POST" action="{{ route('admin.known-threats.destroy', $threat->id) }}" style="display: inline; margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to remove this threat indicator from the blocklist?')" class="remove-btn">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $threats->firstItem() ?? 0 }} to {{ $threats->lastItem() ?? 0 }} of {{ $threats->total() }} threats
        </div>
        <div class="pagination-controls">
            @if ($threats->onFirstPage())
                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $threats->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach ($threats->getUrlRange(1, $threats->lastPage()) as $page => $url)
                @if ($page == $threats->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if ($threats->hasMorePages())
                <a href="{{ $threats->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-shield-alt"></i>
        <p>No known threats in the database.</p>
        @if(Auth::user()->isAdmin())
        <p style="color: var(--text-dim); margin-top: 8px;">Add threat indicators using the form above.</p>
        @endif
    </div>
    @endif
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary success feedback
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = 'var(--success-dim)';
        btn.style.color = 'var(--success)';
        
        setTimeout(function() {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
            btn.style.color = '';
        }, 1500);
    }).catch(function(err) {
        console.error('Failed to copy:', err);
        alert('Failed to copy to clipboard');
    });
}
</script>
@endsection
