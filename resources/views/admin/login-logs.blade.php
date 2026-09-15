@extends('layouts.app')

@section('title', 'Login Logs - SentryDesk')

@section('page-context-title', 'Login Activity & Security')

@section('content')
<style>
    .filters-section {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .filter-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
        display: block;
    }

    .filter-input {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-input);
        border: 1px solid var(--line);
        border-radius: 9px;
        color: var(--text);
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color 0.2s;
    }

    .filter-input:focus {
        border-color: var(--cyan);
    }

    .filter-btn {
        padding: 10px 20px;
        background: var(--cyan);
        color: #04211E;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .filter-btn:hover {
        opacity: 0.85;
    }

    .logs-table {
        width: 100%;
        background: var(--bg-card);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .logs-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .logs-table thead {
        background: rgba(52, 228, 214, 0.08);
    }

    .logs-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-bottom: 1px solid var(--line);
    }

    .logs-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--line-soft);
        font-size: 13px;
        color: var(--text);
    }

    .logs-table tbody tr:hover {
        background: rgba(52, 228, 214, 0.04);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-success {
        background: var(--success-dim);
        color: var(--success);
    }

    .status-failed {
        background: var(--rose-dim);
        color: var(--rose);
    }

    .action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .btn-block {
        background: var(--rose);
        color: white;
    }

    .btn-unblock {
        background: var(--success);
        color: white;
    }

    .action-btn:hover {
        opacity: 0.8;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
    }

    .pagination a, .pagination span {
        padding: 8px 14px;
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: 8px;
        color: var(--text);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: var(--cyan-dim);
        border-color: var(--cyan);
    }

    .pagination .active {
        background: var(--cyan);
        color: #04211E;
        border-color: var(--cyan);
    }

    .alert-success {
        background: var(--success-dim);
        border: 1px solid var(--success);
        color: var(--success);
        padding: 12px 16px;
        border-radius: 9px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .alert-error {
        background: var(--rose-dim);
        border: 1px solid var(--rose);
        color: var(--rose);
        padding: 12px 16px;
        border-radius: 9px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .user-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .user-email {
        font-weight: 600;
    }

    .user-id {
        font-size: 11px;
        color: var(--text-faint);
    }

    .blocked-badge {
        display: inline-block;
        padding: 2px 8px;
        background: var(--rose-dim);
        color: var(--rose);
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 8px;
    }
</style>

@if (session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i>
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif

<div class="filters-section">
    <form method="GET" action="{{ route('admin.login-logs') }}">
        <div class="filter-row">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-input">
                    <option value="">All Statuses</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Email</label>
                <input type="text" name="email" class="filter-input" placeholder="Search by email..." value="{{ request('email') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Date</label>
                <input type="date" name="date" class="filter-input" value="{{ request('date') }}">
            </div>
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.login-logs') }}" class="filter-btn" style="background: var(--line); color: var(--text); text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
    </form>
</div>

<div class="logs-table">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Date & Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>
                        <div class="user-cell">
                            @if ($log->user)
                                <span class="user-email">{{ $log->user->name }}</span>
                                <span class="user-id">{{ ucfirst($log->user->role) }}
                                    @if ($log->user->is_blocked)
                                        <span class="blocked-badge">Blocked</span>
                                    @endif
                                </span>
                            @else
                                <span class="user-id">User not found</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $log->email }}</td>
                    <td style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">{{ $log->ip_address }}</td>
                    <td>
                        <span class="status-badge {{ $log->status == 'success' ? 'status-success' : 'status-failed' }}">
                            <i class="fas {{ $log->status == 'success' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $log->status }}
                        </span>
                    </td>
                    <td style="color: var(--text-dim); font-size: 12px;">{{ $log->failure_reason ?? '—' }}</td>
                    <td style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                        <br>
                        <span style="color: var(--text-faint); font-size: 11px;">{{ $log->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        @if ($log->user && !$log->user->isAdmin())
                            @if ($log->user->is_blocked)
                                <form method="POST" action="{{ route('admin.login-logs.unblock', $log->user->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="action-btn btn-unblock">
                                        <i class="fas fa-unlock"></i> Unblock
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.login-logs.block', $log->user->id) }}" style="display: inline;" onsubmit="return confirm('Block this user? They will not be able to log in.')">
                                    @csrf
                                    <button type="submit" class="action-btn btn-block">
                                        <i class="fas fa-ban"></i> Block
                                    </button>
                                </form>
                            @endif
                        @else
                            <span style="color: var(--text-faint); font-size: 11px;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-dim);">
                        <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <br>
                        No login logs found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $logs->links() }}
</div>

@endsection
