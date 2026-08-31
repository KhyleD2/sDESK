@extends('layouts.app')

@section('title', 'Admin Dashboard - SentryDesk')

@section('page-context-title', 'Command Overview')

@section('ticker-content')
    <div class="ticker-item">
        <span class="ticker-count-high">{{ $totalPending }}</span> OPEN CASES
        @if(isset($severityBreakdown['critical']) && $severityBreakdown['critical'] > 0)
            — <span class="ticker-count-high">{{ $severityBreakdown['critical'] }}</span> CRITICAL
        @endif
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        LAST ACTIVITY: {{ $recentActivity->first() ? strtoupper($recentActivity->first()->created_at->diffForHumans()) : 'NONE' }}
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        THREAT INTEL: <span class="ticker-status-ok">VIRUSTOTAL — CONNECTED</span>
    </div>
@endsection

@section('content')
<style>
    /* Stat Cards with SOC aesthetic */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .stat-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }
    
    .stat-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        opacity: 0.08;
        pointer-events: none;
    }
    
    .stat-card.card-cyan::after { background: var(--cyan); }
    .stat-card.card-amber::after { background: var(--amber); }
    .stat-card.card-violet::after { background: var(--violet); }
    .stat-card.card-success::after { background: var(--success); }
    
    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }
    
    .stat-icon.icon-cyan { background: var(--cyan-dim); color: var(--cyan); }
    .stat-icon.icon-amber { background: var(--amber-dim); color: var(--amber); }
    .stat-icon.icon-violet { background: var(--violet-dim); color: var(--violet); }
    .stat-icon.icon-success { background: var(--success-dim); color: var(--success); }
    
    .stat-label-top {
        font-family: 'Inter', sans-serif;
        font-size: 9px;
        font-weight: 700;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-value {
        font-family: 'Inter', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        margin-bottom: 6px;
        letter-spacing: -1px;
        font-feature-settings: 'zero' 0;
        font-variant-numeric: normal;
    }
    
    .stat-label-bottom {
        font-size: 12.5px;
        color: var(--text-dim);
        font-weight: 500;
    }
    
    /* Panels */
    .panels-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 20px;
    }
    
    .panel {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 22px;
        transition: box-shadow 0.2s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .panel:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }
    
    .panel-header {
        margin-bottom: 20px;
    }
    
    .panel-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
        letter-spacing: -0.2px;
    }
    
    .panel-subtitle {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9px;
        font-weight: 600;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Severity Bars */
    .severity-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .severity-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .severity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .severity-dot.dot-critical { background: var(--rose); box-shadow: 0 0 8px var(--rose); }
    .severity-dot.dot-high { background: var(--amber); }
    .severity-dot.dot-medium { background: var(--medium); }
    .severity-dot.dot-low { background: var(--success); }
    
    .severity-label {
        font-size: 13px;
        color: var(--text);
        font-weight: 500;
        width: 70px;
    }
    
    .severity-bar-track {
        flex: 1;
        height: 5px;
        background: var(--line-soft);
        border-radius: 3px;
        overflow: hidden;
        position: relative;
    }
    
    .severity-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }
    
    .severity-bar-fill.fill-critical { background: var(--rose); }
    .severity-bar-fill.fill-high { background: var(--amber); }
    .severity-bar-fill.fill-medium { background: var(--medium); }
    .severity-bar-fill.fill-low { background: var(--success); }
    
    .severity-count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dim);
        width: 30px;
        text-align: right;
    }
    
    /* Chart Container */
    .chart-container {
        position: relative;
        height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .chart-canvas-wrapper {
        max-width: 200px;
        max-height: 200px;
    }
    
    .users-chart-wrapper {
        height: 240px;
    }
    
    /* Activity Feed */
    .activity-panel {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    
    .activity-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.2px;
    }
    
    .activity-link {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        color: var(--cyan);
        text-decoration: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: color 0.2s;
    }
    
    .activity-link:hover {
        color: #5AEEE3;
    }
    
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    
    .activity-item {
        display: flex;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .activity-item:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .activity-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }
    
    .activity-dot.dot-create { background: var(--cyan); }
    .activity-dot.dot-update { background: var(--violet); }
    .activity-dot.dot-resolve { background: var(--success); }
    .activity-dot.dot-assign { background: var(--amber); }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-message {
        font-size: 13px;
        color: var(--text);
        line-height: 1.5;
        margin-bottom: 4px;
    }
    
    .activity-message strong {
        font-weight: 600;
        color: var(--text);
    }
    
    .activity-meta {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        color: var(--text-faint);
        text-transform: uppercase;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-faint);
    }
</style>

{{-- Stat Cards --}}
<div class="stat-cards">
    <div class="stat-card card-cyan">
        <div class="stat-card-header">
            <div class="stat-icon icon-cyan"><i class="fas fa-file-alt"></i></div>
            <div class="stat-label-top">ALL-TIME</div>
        </div>
        <div class="stat-value">{{ str_pad($totalReports, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-label-bottom">Total Reports</div>
    </div>
    
    <div class="stat-card card-amber">
        <div class="stat-card-header">
            <div class="stat-icon icon-amber"><i class="fas fa-clock"></i></div>
            <div class="stat-label-top">QUEUE</div>
        </div>
        <div class="stat-value">{{ str_pad($totalPending, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-label-bottom">Pending Review</div>
    </div>
    
    <div class="stat-card card-violet">
        <div class="stat-card-header">
            <div class="stat-icon icon-violet"><i class="fas fa-sync-alt"></i></div>
            <div class="stat-label-top">ACTIVE</div>
        </div>
        <div class="stat-value">{{ str_pad($inProgress, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-label-bottom">In Progress</div>
    </div>
    
    <div class="stat-card card-success">
        <div class="stat-card-header">
            <div class="stat-icon icon-success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-label-top">CLOSED</div>
        </div>
        <div class="stat-value">{{ str_pad($resolved, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-label-bottom">Resolved</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="panels-row">
    {{-- Open Cases by Severity --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Open Cases by Severity</div>
            <div class="panel-subtitle">ACTIVE QUEUE DISTRIBUTION</div>
        </div>
        <div class="severity-list">
            <div class="severity-item">
                <div class="severity-dot dot-critical"></div>
                <div class="severity-label">Critical</div>
                <div class="severity-bar-track">
                    <div class="severity-bar-fill fill-critical" style="width: {{ $totalPending > 0 ? (($severityBreakdown['critical'] ?? 0) / $totalPending * 100) : 0 }}%;"></div>
                </div>
                <div class="severity-count">{{ str_pad($severityBreakdown['critical'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="severity-item">
                <div class="severity-dot dot-high"></div>
                <div class="severity-label">High</div>
                <div class="severity-bar-track">
                    <div class="severity-bar-fill fill-high" style="width: {{ $totalPending > 0 ? (($severityBreakdown['high'] ?? 0) / $totalPending * 100) : 0 }}%;"></div>
                </div>
                <div class="severity-count">{{ str_pad($severityBreakdown['high'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="severity-item">
                <div class="severity-dot dot-medium"></div>
                <div class="severity-label">Medium</div>
                <div class="severity-bar-track">
                    <div class="severity-bar-fill fill-medium" style="width: {{ $totalPending > 0 ? (($severityBreakdown['medium'] ?? 0) / $totalPending * 100) : 0 }}%;"></div>
                </div>
                <div class="severity-count">{{ str_pad($severityBreakdown['medium'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="severity-item">
                <div class="severity-dot dot-low"></div>
                <div class="severity-label">Low</div>
                <div class="severity-bar-track">
                    <div class="severity-bar-fill fill-low" style="width: {{ $totalPending > 0 ? (($severityBreakdown['low'] ?? 0) / $totalPending * 100) : 0 }}%;"></div>
                </div>
                <div class="severity-count">{{ str_pad($severityBreakdown['low'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>
    
    {{-- Users Distribution --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">System Users by Role</div>
            <div class="panel-subtitle">REGISTERED ACCOUNTS</div>
        </div>
        <div class="users-chart-wrapper">
            <canvas id="usersChart"></canvas>
        </div>
    </div>
</div>

{{-- Recent Activity --}}
<div class="activity-panel">
    <div class="activity-header">
        <div class="activity-title">Recent System Activity</div>
        <a href="{{ route('admin.activity-logs') }}" class="activity-link">View All →</a>
    </div>
    
    @if($recentActivity->count() > 0)
        <div class="activity-list">
            @foreach($recentActivity->take(8) as $activity)
            <div class="activity-item">
                <div class="activity-dot {{ 
                    str_contains(strtolower($activity->action_description), 'created') || str_contains(strtolower($activity->action_description), 'submitted') ? 'dot-create' : 
                    (str_contains(strtolower($activity->action_description), 'updated') || str_contains(strtolower($activity->action_description), 'changed') ? 'dot-update' : 
                    (str_contains(strtolower($activity->action_description), 'resolved') || str_contains(strtolower($activity->action_description), 'closed') ? 'dot-resolve' : 'dot-assign')) 
                }}"></div>
                <div class="activity-content">
                    <div class="activity-message">
                        <strong>{{ $activity->user->name }}</strong> {{ $activity->action_description }}
                    </div>
                    <div class="activity-meta">
                        Report #{{ str_pad($activity->report_id ?? 0, 3, '0', STR_PAD_LEFT) }} · {{ strtoupper($activity->created_at->diffForHumans()) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <p>No recent activity to display.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Users Bar Chart
    const usersData = {
        users: {{ $usersByRole['user'] ?? 0 }},
        analysts: {{ $usersByRole['analyst'] ?? 0 }},
        admins: {{ $usersByRole['admin'] ?? 0 }}
    };
    
    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        new Chart(usersCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Users', 'Analysts', 'Admins'],
                datasets: [{
                    label: 'Count',
                    data: [usersData.users, usersData.analysts, usersData.admins],
                    backgroundColor: ['#34E4D6', '#9B8CFF', '#FF5C7A'],
                    borderWidth: 0,
                    borderRadius: 6,
                    barThickness: 70
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 800,
                    easing: 'easeInOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            color: '#7C8698',
                            font: {
                                family: "'IBM Plex Mono', monospace",
                                size: 11
                            },
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            color: '#1A2029',
                            drawBorder: false,
                            lineWidth: 1
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: '#7C8698',
                            font: {
                                family: "'IBM Plex Mono', monospace",
                                size: 11,
                                weight: 500
                            }
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(19, 24, 34, 0.95)',
                        titleColor: '#E7EBF2',
                        bodyColor: '#7C8698',
                        borderColor: '#212836',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        cornerRadius: 8,
                        titleFont: { 
                            family: "'IBM Plex Mono', monospace",
                            size: 12, 
                            weight: 600 
                        },
                        bodyFont: { 
                            family: "'IBM Plex Mono', monospace",
                            size: 11 
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
