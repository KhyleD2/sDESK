@extends('layouts.app')

@section('title', 'Edit User - SentryDesk')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1>Edit User: {{ $user->name }}</h1>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" style="margin-top: 20px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px;">
            @error('name')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 10px;">
            @error('email')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Role *</label>
            <select name="role" required style="width: 100%; padding: 10px;">
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                <option value="analyst" {{ $user->role == 'analyst' ? 'selected' : '' }}>Analyst</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">New Password (leave blank to keep current)</label>
            <input type="password" name="password" style="width: 100%; padding: 10px;">
            @error('password')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Confirm New Password</label>
            <input type="password" name="password_confirmation" style="width: 100%; padding: 10px;">
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" style="padding: 12px 30px; background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px;">
                Update User
            </button>
            <a href="{{ route('admin.users.index') }}" style="padding: 12px 30px; background: #6c757d; color: white; text-decoration: none; margin-left: 10px;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
