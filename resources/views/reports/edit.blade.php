@extends('layouts.app')

@section('title', 'Edit Report #'.$report->id.' - SentryDesk')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1>Edit Threat Report #{{ $report->id }}</h1>

    <form method="POST" action="{{ route('reports.update', $report->id) }}" style="margin-top: 20px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
            <label for="severity" style="display: block; margin-bottom: 5px; font-weight: bold;">Severity Level</label>
            <select id="severity" name="severity" style="width: 100%; padding: 10px; font-size: 16px;">
                <option value="low" {{ $report->severity == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ $report->severity == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ $report->severity == 'high' ? 'selected' : '' }}>High</option>
                <option value="critical" {{ $report->severity == 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            @error('severity')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="status" style="display: block; margin-bottom: 5px; font-weight: bold;">Status</label>
            <select id="status" name="status" style="width: 100%; padding: 10px; font-size: 16px;">
                <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="under_review" {{ $report->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="in_progress" {{ $report->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            @error('status')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="verdict" style="display: block; margin-bottom: 5px; font-weight: bold;">Verdict</label>
            <select id="verdict" name="verdict" style="width: 100%; padding: 10px; font-size: 16px;">
                <option value="pending" {{ $report->verdict == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed_threat" {{ $report->verdict == 'confirmed_threat' ? 'selected' : '' }}>Confirmed Threat</option>
                <option value="false_positive" {{ $report->verdict == 'false_positive' ? 'selected' : '' }}>False Positive</option>
                <option value="escalated_externally" {{ $report->verdict == 'escalated_externally' ? 'selected' : '' }}>Escalated Externally</option>
            </select>
            @error('verdict')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="scan_result" style="display: block; margin-bottom: 5px; font-weight: bold;">Scan Result (Optional)</label>
            <textarea id="scan_result" name="scan_result" rows="4" style="width: 100%; padding: 10px; font-size: 16px;">{{ $report->scan_result }}</textarea>
            @error('scan_result')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="escalation_note" style="display: block; margin-bottom: 5px; font-weight: bold;">Escalation Note (Optional)</label>
            <textarea id="escalation_note" name="escalation_note" rows="4" style="width: 100%; padding: 10px; font-size: 16px;">{{ $report->escalation_note }}</textarea>
            @error('escalation_note')
                <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" style="padding: 12px 30px; background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; border-radius: 5px;">
                Update Report
            </button>
            <a href="{{ route('reports.show', $report->id) }}" style="padding: 12px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px; display: inline-block;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
