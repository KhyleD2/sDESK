@extends('layouts.app')

@section('title', 'Profile Settings - SentryDesk')

@section('page-context-title', 'Profile Settings')

@section('content')
<style>
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .profile-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        margin-bottom: 24px;
    }

    [data-theme="light"] .profile-card {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--line-soft);
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--cyan);
        font-size: 15px;
    }

    .profile-picture-section {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 28px;
        padding: 20px;
        background: var(--bg);
        border-radius: 12px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--cyan-dim);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 700;
        color: var(--cyan);
        flex-shrink: 0;
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        background: var(--bg);
        border: 1.5px solid var(--line-soft);
        border-radius: 10px;
        padding: 14px 18px;
        color: var(--text);
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-faint);
        margin-top: 6px;
    }

    .error-message {
        color: var(--rose);
        font-size: 12px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .success-message {
        background: var(--success-dim);
        border: 1px solid var(--success);
        color: var(--success);
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary {
        background: var(--cyan);
        color: #04211E;
        border: none;
        padding: 13px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #5AEEE3;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--line-soft);
        color: var(--text);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: var(--line);
    }

    .btn-danger {
        background: var(--rose-dim);
        color: var(--rose);
        border: 1px solid var(--rose);
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-danger:hover {
        background: var(--rose);
        color: white;
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: -9999px;
    }

    .divider {
        height: 1px;
        background: var(--line-soft);
        margin: 32px 0;
    }
</style>

<div class="profile-container">
    @if (session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Profile Picture Section --}}
        <div class="profile-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-image"></i> Profile Picture
                </div>
            </div>

            <div class="profile-picture-section">
                <div class="profile-avatar">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="avatar-actions">
                    <div class="file-input-wrapper">
                        <label for="profile_picture" class="btn-secondary" style="cursor: pointer; margin: 0;">
                            <i class="fas fa-upload"></i> Choose Photo
                        </label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(this)">
                    </div>
                    @if($user->profile_picture)
                        <button type="button" class="btn-danger" onclick="event.preventDefault(); if(confirm('Remove profile picture?')) document.getElementById('remove-picture-form').submit();">
                            <i class="fas fa-trash"></i> Remove Photo
                        </button>
                    @endif
                    <span class="form-hint">JPG, PNG or GIF. Max 2MB.</span>
                </div>
            </div>
            @error('profile_picture')
                <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Basic Information --}}
        <div class="profile-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-user"></i> Basic Information
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                @error('name')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                @error('email')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" value="{{ ucfirst($user->role) }}" disabled class="form-input" style="opacity: 0.6; cursor: not-allowed;">
                <span class="form-hint">Your role cannot be changed.</span>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="profile-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-lock"></i> Change Password
                </div>
            </div>

            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Enter current password">
                <span class="form-hint">Required only if you want to change your password.</span>
                @error('current_password')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Enter new password">
                <span class="form-hint">Minimum 8 characters.</span>
                @error('new_password')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input" placeholder="Confirm new password">
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ url()->previous() }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- Remove Picture Form --}}
@if($user->profile_picture)
<form id="remove-picture-form" method="POST" action="{{ route('profile.remove-picture') }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatar = document.querySelector('.profile-avatar');
            avatar.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
