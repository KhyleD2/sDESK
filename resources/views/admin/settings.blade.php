@extends('layouts.app')

@section('title', 'Settings - SentryDesk')

@section('content')
<style>
    .settings-container {
        padding: 0;
        max-width: 1400px;
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
    
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .settings-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .settings-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
        letter-spacing: -0.3px;
    }
    
    .card-description {
        color: var(--text-dim);
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .add-category-form {
        background: var(--cyan-dim);
        border: 1px solid var(--cyan);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .form-row {
        display: flex;
        gap: 16px;
        align-items: end;
    }
    
    .form-group {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .form-label {
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
    }
    
    .form-input {
        background: var(--bg);
        border: 1px solid var(--line);
        color: var(--text);
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 14px;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-input::placeholder {
        color: var(--text-faint);
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
    }
    
    .form-input:hover {
        border-color: var(--line-soft);
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
        transition: opacity 0.2s;
    }
    
    .add-btn:hover {
        opacity: 0.9;
    }
    
    .update-btn {
        background: var(--cyan);
        color: var(--bg);
        border: none;
        padding: 11px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .update-btn:hover {
        opacity: 0.9;
    }
    
    .delete-btn {
        background: var(--rose-dim);
        color: var(--rose);
        border: 1px solid var(--rose);
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .delete-btn:hover {
        opacity: 0.9;
    }
    
    .categories-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .categories-table thead {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .categories-table thead {
        border-bottom: 1px solid var(--line);
    }
    
    .categories-table thead th {
        padding: 12px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-dim);
    }
    
    .categories-table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .categories-table tbody tr {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .categories-table tbody td {
        padding: 14px 16px;
        color: var(--text);
        font-size: 14px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .info-row {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: var(--text-dim);
        font-size: 14px;
        font-weight: 500;
    }
    
    .info-value {
        color: var(--text);
        font-size: 14px;
        font-weight: 600;
    }
    
    .toggle-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    [data-theme="light"] .toggle-switch {
        border-bottom: 1px solid var(--line-soft);
    }
    
    .toggle-switch:last-child {
        border-bottom: none;
    }
    
    .toggle-label {
        color: var(--text);
        font-size: 14px;
        font-weight: 500;
    }
    
    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--line-soft);
        transition: .4s;
        border-radius: 24px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: var(--text);
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: var(--success);
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    
    .form-group-full {
        grid-column: 1 / -1;
    }
    
    .error-message {
        color: var(--rose);
        font-size: 13px;
        margin-top: 6px;
    }
</style>

<div class="settings-container">
    <div class="page-header">
        <h1>System Settings</h1>
        <p>Manage system configuration and preferences</p>
    </div>

    <div class="settings-grid">
        <!-- Manage Threat Categories -->
        <div class="settings-card">
            <h3 class="card-title">Manage Threat Categories</h3>
            <p class="card-description">Add, edit, or remove threat categories used throughout the system.</p>

            <!-- Add New Category Form -->
            <div class="add-category-form">
                <form method="POST" action="{{ route('admin.settings.categories.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">New Category Name</label>
                            <input type="text" name="name" required class="form-input" placeholder="e.g., Ransomware">
                            @error('name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="add-btn">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Categories Table -->
            <table class="categories-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Reports Using This</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td style="color: var(--text-dim);">{{ $category->threatReports()->count() }} reports</td>
                        <td>
                            <form method="POST" action="{{ route('admin.settings.categories.delete', $category->id) }}" style="display: inline; margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this category? This will fail if reports are using it.')" class="delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Notification Preferences -->
        <div class="settings-card">
            <h3 class="card-title">Notification Preferences</h3>
            <p class="card-description">Configure how you receive system notifications.</p>

            <div class="toggle-switch">
                <span class="toggle-label">Email Notifications</span>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="toggle-switch">
                <span class="toggle-label">In-App Notifications</span>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            
            <p style="color: var(--text-faint); font-size: 13px; margin-top: 16px;">
                <i class="fas fa-info-circle"></i> These settings control your personal notification preferences.
            </p>
        </div>

        <!-- Account Settings -->
        <div class="settings-card">
            <h3 class="card-title">Account Settings</h3>
            <p class="card-description">Update your personal account information.</p>

            <form method="POST" action="{{ route('admin.settings.account.update') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" required class="form-input">
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required class="form-input">
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••">
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="update-btn">
                    <i class="fas fa-save"></i> Update Account
                </button>
            </form>
        </div>

        <!-- System Information -->
        <div class="settings-card">
            <h3 class="card-title">System Information</h3>
            <p class="card-description">Read-only system details and configuration.</p>

            <div class="info-row">
                <span class="info-label">Application Name</span>
                <span class="info-value">SentryDesk</span>
            </div>

            <div class="info-row">
                <span class="info-label">Version</span>
                <span class="info-value">1.0.0</span>
            </div>

            <div class="info-row">
                <span class="info-label">Environment</span>
                <span class="info-value">{{ ucfirst(config('app.env')) }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Database</span>
                <span class="info-value">MySQL</span>
            </div>
        </div>
    </div>
</div>
@endsection
