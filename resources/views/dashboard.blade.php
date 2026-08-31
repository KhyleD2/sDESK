@extends('layouts.app')

@section('title', 'Dashboard - SentryDesk')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <h1>Welcome to SentryDesk, {{ Auth::user()->name }}!</h1>
    <p>Role: <strong>{{ strtoupper(Auth::user()->role) }}</strong></p>

    <div style="margin-top: 30px;">
        <h2>Quick Actions</h2>
        <div style="display: flex; gap: 15px; margin-top: 15px;">
            @if(Auth::user()->isUser())
            <a href="{{ route('reports.create') }}" style="padding: 15px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                Submit New Threat Report
            </a>
            @endif
            <a href="{{ route('reports.index') }}" style="padding: 15px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">
                View {{ Auth::user()->isUser() ? 'My' : 'All' }} Reports
            </a>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isAnalyst())
    <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6;">
        <h3>{{ Auth::user()->isAdmin() ? 'Admin' : 'Analyst' }} Panel</h3>
        <p>You have elevated privileges to review and manage threat reports.</p>
        <p style="color: #666; margin-top: 10px;"><em>Note: Only regular users can submit threat reports. Your role is to investigate and resolve them.</em></p>
    </div>
    @endif
</div>
@endsection
