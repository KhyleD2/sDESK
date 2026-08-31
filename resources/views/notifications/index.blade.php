@extends('layouts.app')

@section('title', 'Notifications - SentryDesk')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Notifications</h1>
        <form method="POST" action="{{ route('notifications.read-all') }}" style="display: inline;">
            @csrf
            <button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer;">
                Mark All as Read
            </button>
        </form>
    </div>

    @if($notifications->count() > 0)
    <div style="margin-top: 20px;">
        @foreach($notifications as $notification)
        <div style="padding: 15px; background: {{ $notification->is_read ? '#f8f9fa' : '#e7f3ff' }}; border-left: 4px solid {{ $notification->is_read ? '#6c757d' : '#007bff' }}; margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <p style="margin: 0;">{{ $notification->message }}</p>
                    <small style="color: #666;">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <div style="display: flex; gap: 10px;">
                    @if($notification->report_id)
                    <a href="{{ route('reports.show', $notification->report_id) }}" style="padding: 5px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 3px;">
                        View Report
                    </a>
                    @endif
                    @if(!$notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="padding: 5px 15px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 3px;">
                            Mark Read
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 20px;">
        {{ $notifications->links() }}
    </div>
    @else
    <p style="padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; margin-top: 20px;">
        No notifications yet.
    </p>
    @endif
</div>
@endsection
