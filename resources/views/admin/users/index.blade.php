@extends('layouts.app')

@section('title', 'User Management - SentryDesk')

@section('content')
<style>
    .user-management-container {
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
    
    .create-btn {
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
        text-decoration: none;
        transition: opacity 0.2s;
    }
    
    .create-btn:hover {
        opacity: 0.9;
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
    
    .filter-input {
        background: var(--bg);
        border: 1px solid var(--line);
        color: var(--text);
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 400;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
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
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .filter-select:hover {
        border-color: var(--line-soft);
    }
    
    .filter-select:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
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
    
    .users-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .users-table thead {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .users-table thead {
        border-bottom: 1px solid var(--line);
    }
    
    .users-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-dim);
        background: var(--bg);
    }
    
    .users-table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.2s;
    }
    
    [data-theme="light"] .users-table tbody tr {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .users-table tbody tr:hover {
        background: var(--cyan-dim);
    }
    
    .users-table tbody td {
        padding: 18px 20px;
        color: var(--text);
        font-size: 14px;
    }
    
    .user-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bg);
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .user-avatar.admin { background: var(--rose); }
    .user-avatar.analyst { background: var(--cyan); }
    .user-avatar.user { background: var(--success); }
    
    .user-name {
        font-weight: 600;
        color: var(--text);
    }
    
    .role-badge {
        display: inline-flex;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .role-badge.admin {
        background: var(--rose);
        color: var(--bg);
    }
    
    .role-badge.analyst {
        background: var(--cyan);
        color: var(--bg);
    }
    
    .role-badge.user {
        background: var(--success);
        color: var(--bg);
    }
    
    .actions-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .edit-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: 6px;
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .edit-btn:hover {
        background: var(--line-soft);
        border-color: var(--line-soft);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .delete-btn {
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
    
    .delete-btn:hover {
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

<div class="user-management-container">
    <div class="page-header">
        <div class="page-title-section">
            <h1>User Management</h1>
            <p>Manage system users and their roles</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="create-btn">
            <i class="fas fa-plus"></i> Create User
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar">
        <div class="filter-grid">
            <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Search by name or email...">
            
            <select name="role" class="filter-select">
                <option value="">All Roles</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="analyst" {{ request('role') == 'analyst' ? 'selected' : '' }}>Analyst</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Search
            </button>
            
            <a href="{{ route('admin.users.index') }}" class="reset-btn">
                <i class="fas fa-redo-alt"></i> Reset
            </a>
        </div>
    </form>

    <!-- Users Table -->
    @if($users->count() > 0)
    <div class="table-card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="user-name-cell">
                            <div class="user-avatar {{ $user->role }}" style="overflow: hidden;">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <span class="user-name">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color: var(--text-dim);">{{ $user->email }}</td>
                    <td>
                        <span class="role-badge {{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td style="color: var(--text-dim);">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
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
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
        </div>
        <div class="pagination-controls">
            @if ($users->onFirstPage())
                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if ($page == $users->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>No users found matching your search.</p>
        <a href="{{ route('admin.users.index') }}" style="color: #3B82F6; text-decoration: none; font-weight: 600;">Clear filters</a>
    </div>
    @endif
</div>
@endsection
