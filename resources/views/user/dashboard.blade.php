@extends('layouts.app')

@section('title', 'My Dashboard - SentryDesk')

@section('page-context-title', 'User Dashboard')

@section('content')
<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* ── Hero greeting ─────────────────────────────── */
    .hero-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        background: var(--bg-card);
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        border-left: 4px solid var(--cyan);
        position: relative;
        overflow: hidden;
    }

    [data-theme="light"] .hero-row {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .hero-row::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: var(--cyan-dim);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-greeting {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.4px;
        margin-bottom: 6px;
    }

    .hero-greeting span {
        color: var(--cyan);
    }

    .hero-subtitle {
        font-size: 14px;
        color: var(--text-dim);
        font-weight: 400;
    }

    .hero-submit-btn {
        background: var(--cyan);
        color: #04211E;
        border: none;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
        z-index: 1;
        box-shadow: 0 4px 14px rgba(52,228,214,0.3);
    }

    .hero-submit-btn:hover {
        background: #5AEEE3;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(52,228,214,0.4);
    }

    /* ── Stat Cards ──────────────────────────────── */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 18px 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        cursor: pointer;
        border-top: 3px solid transparent;
    }

    [data-theme="light"] .stat-card {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-card.cyan  { border-top-color: var(--cyan); }
    .stat-card.orange{ border-top-color: var(--amber); }
    .stat-card.purple{ border-top-color: var(--violet); }
    .stat-card.blue  { border-top-color: #3B82F6; }
    .stat-card.green { border-top-color: var(--success); }

    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .stat-card-icon.cyan   { background: var(--cyan-dim);   color: var(--cyan); }
    .stat-card-icon.orange { background: var(--amber-dim);  color: var(--amber); }
    .stat-card-icon.purple { background: var(--violet-dim); color: var(--violet); }
    .stat-card-icon.blue   { background: rgba(59,130,246,.15); color: #3B82F6; }
    .stat-card-icon.green  { background: var(--success-dim); color: var(--success); }

    .stat-card-value {
        font-size: 38px;
        font-weight: 700;
        line-height: 1;
        font-family: 'Inter', sans-serif;
        font-feature-settings: 'zero' 0;
        font-variant-numeric: normal;
        color: var(--text);
    }

    .stat-card-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-dim);
    }

    .stat-card-sublabel {
        font-size: 11px;
        color: var(--text-faint);
        font-weight: 500;
    }

    /* ── Two-col layout ──────────────────────────── */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    /* ── Dashboard Card ──────────────────────────── */
    .dashboard-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 20px 22px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }

    [data-theme="light"] .dashboard-card {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--cyan);
        font-size: 14px;
    }

    .view-all-link {
        color: var(--cyan);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .view-all-link:hover { color: #5AEEE3; }

    /* ── Severity Donut ──────────────────────────── */
    .donut-section {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .donut-wrapper {
        flex-shrink: 0;
        width: 180px;
        height: 180px;
        position: relative;
    }

    .donut-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }

    .donut-percentage {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
    }

    .donut-label {
        font-size: 10px;
        color: var(--text-faint);
        margin-top: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .donut-total {
        font-size: 11px;
        color: var(--text-dim);
        margin-top: 2px;
    }

    .donut-legend {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: rgba(255,255,255,0.03);
        border-radius: 7px;
        transition: background 0.2s;
    }

    [data-theme="light"] .legend-item {
        background: rgba(0,0,0,0.03);
    }

    .legend-item:hover {
        background: rgba(255,255,255,0.06);
    }

    [data-theme="light"] .legend-item:hover {
        background: rgba(0,0,0,0.06);
    }

    .legend-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-label {
        font-size: 12px;
        color: var(--text);
        font-weight: 500;
    }

    .legend-count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-dim);
    }

    /* ── Activity / Notification timeline ─────────── */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 14px;
        background: rgba(255,255,255,0.03);
        border-radius: 9px;
        border-left: 3px solid var(--cyan);
        transition: background 0.2s;
    }

    [data-theme="light"] .activity-item {
        background: rgba(0,0,0,0.03);
    }

    .activity-item:hover {
        background: var(--cyan-dim);
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--cyan-dim);
        color: var(--cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .activity-body {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .activity-desc {
        font-size: 12px;
        color: var(--text-dim);
        margin-bottom: 4px;
    }

    .activity-time {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* ── Recent Reports Table ─────────────────────── */
    .reports-table {
        width: 100%;
        border-collapse: collapse;
    }

    .reports-table thead th {
        background: var(--bg);
        color: var(--text-dim);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    [data-theme="light"] .reports-table thead th {
        border-bottom: 1px solid var(--line);
    }

    .reports-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: background 0.2s;
        cursor: pointer;
    }

    [data-theme="light"] .reports-table tbody tr {
        border-bottom: 1px solid var(--line);
    }

    .reports-table tbody tr:hover {
        background: var(--cyan-dim);
    }

    .reports-table tbody tr:last-child {
        border-bottom: none;
    }

    .reports-table tbody td {
        padding: 14px 16px;
        color: var(--text);
        font-size: 13px;
        vertical-align: middle;
    }

    .report-id {
        font-family: 'IBM Plex Mono', monospace;
        color: var(--cyan);
        font-weight: 700;
        font-size: 12px;
    }

    .report-cat {
        color: var(--text);
        font-weight: 500;
    }

    .severity-badge, .status-badge {
        font-family: 'IBM Plex Mono', monospace;
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 700;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Severity */
    .severity-badge.critical { background: var(--rose-dim);    color: var(--rose); }
    .severity-badge.high     { background: var(--amber-dim);   color: var(--amber); }
    .severity-badge.medium   { background: rgba(233,213,102,.15); color: var(--medium); }
    .severity-badge.low      { background: var(--success-dim); color: var(--success); }

    /* Status */
    .status-badge.resolved     { background: var(--success-dim); color: var(--success); }
    .status-badge.under_review { background: var(--violet-dim);  color: var(--violet); }
    .status-badge.in_progress  { background: rgba(59,130,246,.15); color: #3B82F6; }
    .status-badge.pending      { background: var(--amber-dim);   color: var(--amber); }

    .report-date {
        font-family: 'IBM Plex Mono', monospace;
        color: var(--text-dim);
        font-size: 11px;
    }

    /* ── Empty State ─────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 36px 20px;
        color: var(--text-faint);
        font-size: 13px;
    }

    .empty-state i {
        font-size: 38px;
        opacity: 0.25;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state-link {
        color: var(--cyan);
        text-decoration: none;
        font-weight: 600;
    }

    .empty-state-link:hover { color: #5AEEE3; }
</style>

<div class="dashboard-container">

    {{-- Hero Greeting --}}
    <div class="hero-row">
        <div>
            <div class="hero-greeting"><i class="fas fa-shield-halved" style="color: var(--cyan); font-size: 20px; margin-right: 4px;"></i> Welcome back, <span>{{ Auth::user()->name }}</span></div>
            <div class="hero-subtitle">Here's an overview of your threat reports and recent activity.</div>
        </div>
        <a href="{{ route('reports.create') }}" class="hero-submit-btn">
            <i class="fas fa-plus-circle"></i> Submit New Report
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards">
        <div class="stat-card cyan">
            <div class="stat-card-top">
                <div class="stat-card-label">Total Reports</div>
                <div class="stat-card-icon cyan"><i class="fas fa-file-alt"></i></div>
            </div>
            <div class="stat-card-value">{{ $myTotalReports }}</div>
            <div class="stat-card-sublabel">All-time submissions</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-card-top">
                <div class="stat-card-label">Pending</div>
                <div class="stat-card-icon orange"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-card-value">{{ $myPending }}</div>
            <div class="stat-card-sublabel">Awaiting review</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-card-top">
                <div class="stat-card-label">Under Review</div>
                <div class="stat-card-icon purple"><i class="fas fa-eye"></i></div>
            </div>
            <div class="stat-card-value">{{ $myUnderReview }}</div>
            <div class="stat-card-sublabel">Being examined</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-card-top">
                <div class="stat-card-label">In Progress</div>
                <div class="stat-card-icon blue"><i class="fas fa-sync-alt"></i></div>
            </div>
            <div class="stat-card-value">{{ $myInProgress }}</div>
            <div class="stat-card-sublabel">Investigation ongoing</div>
        </div>

        <div class="stat-card green">
            <div class="stat-card-top">
                <div class="stat-card-label">Resolved</div>
                <div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-card-value">{{ $myResolved }}</div>
            <div class="stat-card-sublabel">Completed cases</div>
        </div>
    </div>

    {{-- Content Grid: Severity Donut + Activity Feed --}}
    <div class="content-grid">

        {{-- Reports by Severity --}}
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-pie"></i> Reports by Severity</div>
            </div>
            <div class="donut-section">
                <div class="donut-wrapper">
                    <canvas id="severityChart"></canvas>
                    <div class="donut-center-text">
                        <div class="donut-percentage" id="donutPct">—</div>
                        <div class="donut-label">Highest</div>
                        <div class="donut-total">Total: {{ $myTotalReports }}</div>
                    </div>
                </div>
                <div class="donut-legend" id="severityLegend"></div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-bell"></i> Recent Activity</div>
                <a href="{{ route('reports.index') }}" class="view-all-link">View all <i class="fas fa-arrow-right" style="font-size:10px;"></i></a>
            </div>
            @if($myRecentReports->count() > 0)
                <div class="activity-list">
                    @foreach($myRecentReports->take(4) as $report)
                    @php
                        $statusColor = match($report->status) {
                            'resolved'     => 'var(--success)',
                            'in_progress'  => '#3B82F6',
                            'under_review' => 'var(--violet)',
                            default        => 'var(--amber)',
                        };
                        $statusIcon = match($report->status) {
                            'resolved'     => 'fa-check-circle',
                            'in_progress'  => 'fa-sync-alt',
                            'under_review' => 'fa-eye',
                            default        => 'fa-clock',
                        };
                    @endphp
                    <div class="activity-item" style="border-left-color: {{ $statusColor }};">
                        <div class="activity-icon" style="background: rgba(0,0,0,0.08); color: {{ $statusColor }};">
                            <i class="fas {{ $statusIcon }}"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title">Report #{{ $report->id }} · {{ $report->category->name }}</div>
                            <div class="activity-desc">Status updated to <strong>{{ str_replace('_', ' ', ucfirst($report->status)) }}</strong></div>
                            <div class="activity-time">{{ $report->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No recent activity yet.</p>
                    <p><a href="{{ route('reports.create') }}" class="empty-state-link">Submit your first report</a></p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Reports Table --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-list-alt"></i> Recent Reports</div>
            <a href="{{ route('reports.index') }}" class="view-all-link">View all <i class="fas fa-arrow-right" style="font-size:10px;"></i></a>
        </div>
        @if($myRecentReports->count() > 0)
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myRecentReports as $report)
                    <tr onclick="window.location.href='{{ route('reports.show', $report->id) }}'">
                        <td><span class="report-id">#{{ $report->id }}</span></td>
                        <td><span class="report-cat">{{ $report->category->name }}</span></td>
                        <td><span class="severity-badge {{ $report->severity }}">{{ ucfirst($report->severity) }}</span></td>
                        <td><span class="status-badge {{ $report->status }}">{{ str_replace('_', ' ', ucfirst($report->status)) }}</span></td>
                        <td><span class="report-date">{{ $report->created_at->format('M d, Y · h:i A') }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard"></i>
                <p>You haven't submitted any reports yet.</p>
                <p><a href="{{ route('reports.create') }}" class="empty-state-link">Submit your first report →</a></p>
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const severityData = {
        critical: {{ $mySeverityStats['critical'] ?? 0 }},
        high:     {{ $mySeverityStats['high'] ?? 0 }},
        medium:   {{ $mySeverityStats['medium'] ?? 0 }},
        low:      {{ $mySeverityStats['low'] ?? 0 }}
    };

    const labels   = ['Critical', 'High', 'Medium', 'Low'];
    const values   = [severityData.critical, severityData.high, severityData.medium, severityData.low];
    const bgColors = ['#FF5C7A', '#F5B942', '#E9D566', '#3DD68C'];
    const severityTotal = values.reduce((a, b) => a + b, 0);

    const canvas = document.getElementById('severityChart');
    const legend = document.getElementById('severityLegend');
    const pctEl  = document.getElementById('donutPct');

    const cs = (v) => getComputedStyle(document.documentElement).getPropertyValue(v).trim();

    if (canvas) {
        if (severityTotal === 0) {
            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['No Data'],
                    datasets: [{ data: [1], backgroundColor: [cs('--line-soft')], borderWidth: 0, cutout: '75%' }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
            pctEl.textContent = '0%';
        } else {
            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: bgColors,
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    animation: { duration: 900, easing: 'easeInOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: cs('--bg-card'),
                            titleColor: cs('--text'),
                            bodyColor: cs('--text-dim'),
                            borderColor: cs('--line'),
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { family: "'IBM Plex Mono', monospace", size: 12, weight: 600 },
                            bodyFont:  { family: "'IBM Plex Mono', monospace", size: 11 }
                        }
                    }
                }
            });

            const maxVal = Math.max(...values);
            pctEl.textContent = Math.round((maxVal / severityTotal) * 100) + '%';
        }

        // Legend
        let html = '';
        labels.forEach((label, i) => {
            const pct = severityTotal > 0 ? ((values[i] / severityTotal) * 100).toFixed(1) : '0.0';
            html += `
                <div class="legend-item">
                    <div class="legend-left">
                        <span class="legend-dot" style="background:${bgColors[i]};"></span>
                        <span class="legend-label">${label}</span>
                    </div>
                    <span class="legend-count">${pct}%</span>
                </div>`;
        });
        legend.innerHTML = html;
    }
});
</script>
@endsection
