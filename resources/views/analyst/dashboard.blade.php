@extends('layouts.app')

@section('title', 'Analyst Dashboard - SentryDesk')

@section('page-context-title', 'Command Overview')

@section('ticker-content')
    <div class="ticker-item">
        REPORTS TODAY: <span class="ticker-status-ok">{{ $todayReports }}</span>
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        UNDER REVIEW: <span class="ticker-count-high">{{ $underReviewCount }}</span>
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        IN PROGRESS: <span>{{ $inProgressCount }}</span>
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        RESOLVED: <span class="ticker-status-ok">{{ $resolvedCount }}</span>
    </div>
@endsection

@section('content')
<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .dashboard-header {
        margin-bottom: 28px;
    }
    
    .dashboard-title {
        font-size: 30px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    
    .dashboard-subtitle {
        color: var(--text-dim);
        font-size: 14px;
        font-weight: 400;
    }
    
    /* Stat Cards */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }
    
    [data-theme="light"] .stat-card {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    [data-theme="light"] .stat-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    
    .stat-card-label {
        color: var(--text-dim);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
    }
    
    .stat-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .stat-card-icon.pending { background: var(--amber-dim); color: var(--amber); }
    .stat-card-icon.review { background: var(--rose-dim); color: var(--rose); }
    .stat-card-icon.progress { background: var(--cyan-dim); color: var(--cyan); }
    .stat-card-icon.resolved { background: var(--success-dim); color: var(--success); }
    
    .stat-card-value {
        font-family: 'Inter', sans-serif;
        font-size: 42px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        font-feature-settings: 'zero' 0;
        font-variant-numeric: normal;
    }
    
    /* Charts Row */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .chart-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .chart-card {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    .chart-header {
        margin-bottom: 20px;
    }
    
    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }
    
    .chart-subtitle {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Donut Chart */
    .donut-section {
        display: flex;
        gap: 24px;
        align-items: center;
    }
    
    .donut-wrapper {
        flex-shrink: 0;
        width: 180px;
        height: 180px;
        position: relative;
    }
    
    .donut-legend {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
    }
    
    [data-theme="light"] .legend-item {
        background: rgba(0, 0, 0, 0.03);
    }
    
    .legend-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .legend-dot {
        width: 7px;
        height: 7px;
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
    
    /* Bar Chart */
    .bar-chart-wrapper {
        height: 220px;
    }
    
    /* Recent Activity */
    .activity-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    [data-theme="light"] .activity-card {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .activity-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
    }
    
    .view-all-link {
        color: var(--cyan);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .view-all-link:hover {
        color: #5AEEE3;
    }
    
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 20px;
    }
    
    .activity-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 14px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 8px;
        transition: background 0.2s;
    }
    
    [data-theme="light"] .activity-item {
        background: rgba(0, 0, 0, 0.02);
    }
    
    .activity-item:hover {
        background: var(--cyan-dim);
    }
    
    .activity-avatar {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--cyan-dim);
        color: var(--cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-user {
        font-weight: 600;
        color: var(--text);
        font-size: 13px;
        margin-bottom: 4px;
    }
    
    .activity-description {
        color: var(--text-dim);
        font-size: 13px;
        margin-bottom: 6px;
    }
    
    .activity-meta {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        color: var(--text-faint);
    }
    
    .queue-btn {
        background: var(--cyan);
        color: #04211E;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .queue-btn:hover {
        background: #5AEEE3;
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Analyst Dashboard</h1>
        <p class="dashboard-subtitle">Queue overview and assigned actions</p>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-label">Pending</div>
                <div class="stat-card-icon pending"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-card-value">{{ str_pad($pendingCount, 2, '0', STR_PAD_LEFT) }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-label">Under Review</div>
                <div class="stat-card-icon review"><i class="fas fa-eye"></i></div>
            </div>
            <div class="stat-card-value">{{ str_pad($underReviewCount, 2, '0', STR_PAD_LEFT) }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-label">In Progress</div>
                <div class="stat-card-icon progress"><i class="fas fa-sync-alt"></i></div>
            </div>
            <div class="stat-card-value">{{ str_pad($inProgressCount, 2, '0', STR_PAD_LEFT) }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-label">Resolved</div>
                <div class="stat-card-icon resolved"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-card-value">{{ str_pad($resolvedCount, 2, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="charts-row">
        {{-- Queue Distribution Donut --}}
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">Queue Distribution</div>
                <div class="chart-subtitle">BY STATUS</div>
            </div>
            <div class="donut-section">
                <div class="donut-wrapper">
                    <canvas id="queueChart"></canvas>
                </div>
                <div class="donut-legend" id="queueLegend"></div>
            </div>
        </div>

        {{-- Priority Overview Bar --}}
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">Priority Overview</div>
                <div class="chart-subtitle">BY SEVERITY</div>
            </div>
            <div class="bar-chart-wrapper">
                <canvas id="priorityChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="activity-card">
        <div class="activity-header">
            <div class="activity-title">Recent Activity</div>
            <a href="{{ route('analyst.activity-logs') }}" class="view-all-link">View all</a>
        </div>
        
        <div class="activity-list">
            @foreach($recentActions->take(4) as $action)
            <div class="activity-item">
                <div class="activity-avatar">
                    {{ strtoupper(substr($action->assignedUser->name ?? 'U', 0, 1)) }}
                </div>
                <div class="activity-content">
                    <div class="activity-user">{{ $action->assignedUser->name ?? 'Unassigned' }}</div>
                    <div class="activity-description">{{ $action->action_type }}</div>
                    <div class="activity-meta">
                        Report #{{ $action->report_id }} • {{ $action->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <a href="{{ route('analyst.report-queue.index') }}" class="queue-btn">
            Go to Report Queue <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const queueData = {
        pending: {{ $pendingCount }},
        under_review: {{ $underReviewCount }},
        in_progress: {{ $inProgressCount }},
        resolved: {{ $resolvedCount }}
    };
    
    const priorityData = {
        critical: {{ $severityStats['critical'] ?? 0 }},
        high: {{ $severityStats['high'] ?? 0 }},
        medium: {{ $severityStats['medium'] ?? 0 }},
        low: {{ $severityStats['low'] ?? 0 }}
    };
    
    // Queue Distribution Donut
    const queueCanvas = document.getElementById('queueChart');
    const queueLegend = document.getElementById('queueLegend');
    
    if (queueCanvas) {
        const labels = ['Pending', 'Under Review', 'In Progress', 'Resolved'];
        const values = [queueData.pending, queueData.under_review, queueData.in_progress, queueData.resolved];
        const colors = ['#F5B942', '#FF5C7A', '#34E4D6', '#3DD68C'];
        const total = values.reduce((a, b) => a + b, 0);
        
        new Chart(queueCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: { duration: 800, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-card').trim(),
                        titleColor: getComputedStyle(document.documentElement).getPropertyValue('--text').trim(),
                        bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--line').trim(),
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: "'IBM Plex Mono', monospace", size: 12, weight: 600 },
                        bodyFont: { family: "'IBM Plex Mono', monospace", size: 11 }
                    }
                }
            }
        });
        
        // Build legend
        let legendHTML = '';
        labels.forEach((label, index) => {
            const value = values[index];
            const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
            legendHTML += `
                <div class="legend-item">
                    <div class="legend-left">
                        <span class="legend-dot" style="background: ${colors[index]};"></span>
                        <span class="legend-label">${label}</span>
                    </div>
                    <span class="legend-count">${percentage}%</span>
                </div>
            `;
        });
        queueLegend.innerHTML = legendHTML;
    }
    
    // Priority Bar Chart
    const priorityCanvas = document.getElementById('priorityChart');
    if (priorityCanvas) {
        new Chart(priorityCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Critical', 'High', 'Medium', 'Low'],
                datasets: [{
                    data: [priorityData.critical, priorityData.high, priorityData.medium, priorityData.low],
                    backgroundColor: ['#FF5C7A', '#F5B942', '#E9D566', '#3DD68C'],
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeInOutQuart' },
                scales: {
                    x: {
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                            font: { family: "'IBM Plex Mono', monospace", size: 11 }
                        },
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                            font: { family: "'IBM Plex Mono', monospace", size: 11 },
                            stepSize: 1
                        },
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--line-soft').trim(),
                            drawBorder: false
                        },
                        border: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-card').trim(),
                        titleColor: getComputedStyle(document.documentElement).getPropertyValue('--text').trim(),
                        bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--line').trim(),
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: "'IBM Plex Mono', monospace", size: 12, weight: 600 },
                        bodyFont: { family: "'IBM Plex Mono', monospace", size: 11 }
                    }
                }
            }
        });
    }
});
</script>
@endsection
