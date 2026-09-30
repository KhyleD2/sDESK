@extends('layouts.app')

@section('title', 'Analytics - SentryDesk')

@section('page-context-title', 'Analytics & Intelligence')

@section('ticker-content')
    <div class="ticker-item">
        <span class="ticker-status-ok">{{ $totalReports }}</span> TOTAL REPORTS ANALYZED
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        {{ $confirmedThreats }} CONFIRMED THREATS
    </div>
    <span class="ticker-divider">·</span>
    <div class="ticker-item">
        DATA REFRESH: <span class="ticker-status-ok">REAL-TIME</span>
    </div>
@endsection

@section('content')
<style>
    /* Stat Cards */
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
    .stat-card.card-rose::after { background: var(--rose); }
    .stat-card.card-success::after { background: var(--success); }
    .stat-card.card-amber::after { background: var(--amber); }
    
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
    .stat-icon.icon-rose { background: var(--rose-dim); color: var(--rose); }
    .stat-icon.icon-success { background: var(--success-dim); color: var(--success); }
    .stat-icon.icon-amber { background: var(--amber-dim); color: var(--amber); }
    
    .stat-label-top {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9px;
        font-weight: 600;
        color: var(--text-faint);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-value {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 32px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        margin-bottom: 6px;
        letter-spacing: -1px;
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
    
    .panels-row-four {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 20px;
    }
    
    .panel-full {
        margin-bottom: 20px;
    }
    
    .panel {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 18px;
        transition: box-shadow 0.2s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .panel:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }
    
    [data-theme="light"] .stat-card,
    [data-theme="light"] .panel {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    [data-theme="light"] .stat-card:hover,
    [data-theme="light"] .panel:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }
    
    [data-theme="light"] .legend-item {
        background: rgba(0, 0, 0, 0.03);
    }
    
    [data-theme="light"] .export-btn {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    [data-theme="light"] .export-btn:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }
    
    .panel-header {
        margin-bottom: 16px;
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
    
    /* Category Bars - Redesigned */
    .category-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    
    .category-item {
        display: grid;
        grid-template-columns: 140px 1fr 50px;
        align-items: center;
        gap: 14px;
    }
    
    .category-label {
        font-size: 13px;
        color: var(--text);
        font-weight: 600;
    }
    
    .category-bar-track {
        height: 22px;
        background: var(--line-soft);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }
    
    .category-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--cyan) 0%, #1AA89C 100%);
        border-radius: 8px;
        transition: width 0.6s ease;
    }
    
    .category-count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        text-align: right;
    }
    
    /* Donut Charts */
    .donut-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }
    
    .donut-chart-wrapper {
        max-width: 180px;
        max-height: 180px;
        position: relative;
    }
    
    .donut-legend {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 10px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
    }
    
    .legend-label-group {
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
        font-size: 11px;
        color: var(--text);
        font-weight: 500;
    }
    
    .legend-count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-dim);
    }
    
    /* Line Chart */
    .line-chart-wrapper {
        height: 220px;
    }
    
    /* Indicator Bars */
    .indicator-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .indicator-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .indicator-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        color: var(--text);
        font-weight: 500;
        width: 80px;
        flex-shrink: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .indicator-bar-track {
        flex: 1;
        height: 18px;
        background: var(--line-soft);
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }
    
    .indicator-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--violet) 0%, #7B6AE8 100%);
        border-radius: 6px;
        transition: width 0.6s ease;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
    }
    
    .indicator-bar-count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        color: #FFFFFF;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        color: var(--text-faint);
        text-transform: uppercase;
    }
    
    .export-btn {
        position: absolute;
        top: 32px;
        right: 32px;
        background: var(--success-dim);
        color: var(--success);
        padding: 10px 18px;
        border-radius: 9px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    
    .export-btn:hover {
        background: rgba(61,214,140,0.18);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }
</style>

<a href="{{ route('admin.analytics.export', ['format' => 'csv']) }}" class="export-btn">
    <i class="fas fa-download"></i> Export CSV
</a>

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
    
    <div class="stat-card card-rose">
        <div class="stat-card-header">
            <div class="stat-icon icon-rose"><i class="fas fa-shield-alt"></i></div>
            <div class="stat-label-top">VERIFIED</div>
        </div>
        <div class="stat-value">{{ str_pad($confirmedThreats, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-label-bottom">Confirmed Threats</div>
    </div>
    
    <div class="stat-card card-success">
        <div class="stat-card-header">
            <div class="stat-icon icon-success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-label-top">CLEARED</div>
        </div>
        <div class="stat-value">{{ str_pad($falsePositives, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-label-bottom">False Positives</div>
    </div>
    
    <div class="stat-card card-amber">
        <div class="stat-card-header">
            <div class="stat-icon icon-amber"><i class="fas fa-clock"></i></div>
            <div class="stat-label-top">AVG TIME</div>
        </div>
        <div class="stat-value">{{ $avgResolutionTime ? number_format($avgResolutionTime, 2) : 'N/A' }}</div>
        <div class="stat-label-bottom">Resolution (days)</div>
    </div>
</div>

{{-- Row 1: Four Donut Charts in One Row --}}
<div class="panels-row-four">
    {{-- Reports by Category Donut --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Reports by Category</div>
            <div class="panel-subtitle">ALL-TIME DISTRIBUTION</div>
        </div>
        <div class="donut-container">
            <div class="donut-chart-wrapper">
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="donut-legend" id="categoryLegend"></div>
        </div>
    </div>
    
    {{-- Severity Distribution --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Severity Distribution</div>
            <div class="panel-subtitle">OPEN + RESOLVED</div>
        </div>
        <div class="donut-container">
            <div class="donut-chart-wrapper">
                <canvas id="severityChart"></canvas>
            </div>
            <div class="donut-legend" id="severityLegend"></div>
        </div>
    </div>
    
    {{-- Verdict Breakdown --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Verdict Breakdown</div>
            <div class="panel-subtitle">ANALYST DECISIONS</div>
        </div>
        <div class="donut-container">
            <div class="donut-chart-wrapper">
                <canvas id="verdictChart"></canvas>
            </div>
            <div class="donut-legend" id="verdictLegend"></div>
        </div>
    </div>
    
    {{-- Status Overview --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Status Overview</div>
            <div class="panel-subtitle">CURRENT LIFECYCLE</div>
        </div>
        <div class="donut-container">
            <div class="donut-chart-wrapper">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="donut-legend" id="statusLegend"></div>
        </div>
    </div>
</div>

{{-- Row 3: Reports Over Time (Full Width) --}}
<div class="panel-full">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Reports Over Time</div>
            <div class="panel-subtitle">LAST 30 DAYS</div>
        </div>
        <div class="line-chart-wrapper">
            <canvas id="timelineChart"></canvas>
        </div>
    </div>
</div>

{{-- Row 4: Top Indicators + Resolution Time Trend --}}
<div class="panels-row">
    {{-- Top Reported Indicators --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Top Reported Indicators</div>
            <div class="panel-subtitle">COMMUNITY INSIGHTS</div>
        </div>
        @if($analyticsData['topIndicators']->count() > 0)
            @php
                $maxIndicatorCount = $analyticsData['topIndicators']->max('times_reported');
            @endphp
            <div class="indicator-list">
                @foreach($analyticsData['topIndicators'] as $item)
                    @php
                        $percentage = $maxIndicatorCount > 0 ? ($item->times_reported / $maxIndicatorCount * 100) : 0;
                        $truncated = strlen($item->indicator) > 20 ? substr($item->indicator, 0, 20) . '...' : $item->indicator;
                    @endphp
                    <div class="indicator-item">
                        <div class="indicator-label" title="{{ $item->indicator }}">{{ $truncated }}</div>
                        <div class="indicator-bar-track">
                            <div class="indicator-bar-fill" style="width: {{ $percentage }}%;">
                                <span class="indicator-bar-count">{{ $item->times_reported }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">No repeated indicators found.</div>
        @endif
    </div>
    
    {{-- Resolution Time Trend --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Resolution Time Trend</div>
            <div class="panel-subtitle">LAST 8 WEEKS</div>
        </div>
        <div class="line-chart-wrapper">
            <canvas id="resolutionChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const analyticsData = @json($analyticsData);
    
    // Color definitions
    const colors = {
        critical: '#FF5C7A',
        high: '#F5B942',
        medium: '#E9D566',
        low: '#3DD68C',
        confirmed_threat: '#FF5C7A',
        false_positive: '#3DD68C',
        escalated_externally: '#9B8CFF',
        pending: '#F5B942',
        under_review: '#34E4D6',
        in_progress: '#9B8CFF',
        resolved: '#3DD68C'
    };
    
    // Helper function to create donut chart with custom legend
    function createDonutChart(canvasId, data, labelMap, colorMap, legendId) {
        const canvas = document.getElementById(canvasId);
        const legendContainer = document.getElementById(legendId);
        
        if (!canvas || !data.length) return;
        
        const labels = data.map(item => labelMap[item[Object.keys(item)[0]]] || item[Object.keys(item)[0]]);
        const values = data.map(item => item.total);
        const bgColors = data.map(item => colorMap[item[Object.keys(item)[0]]] || '#7C8698');
        const total = values.reduce((a, b) => a + b, 0);
        
        // Create chart
        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-card').trim(),
                    cutout: '78%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 800,
                    easing: 'easeInOutQuart'
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
        
        // Create custom legend
        let legendHTML = '';
        data.forEach((item, index) => {
            const label = labels[index];
            const value = values[index];
            const color = bgColors[index];
            legendHTML += `
                <div class="legend-item">
                    <div class="legend-label-group">
                        <span class="legend-dot" style="background: ${color};"></span>
                        <span class="legend-label">${label}</span>
                    </div>
                    <span class="legend-count">${String(value).padStart(2, '0')}</span>
                </div>
            `;
        });
        legendContainer.innerHTML = legendHTML;
    }
    
    // Reports by Category Donut
    const categoryColors = ['#34E4D6', '#9B8CFF', '#F5B942', '#FF5C7A', '#3DD68C', '#E9D566'];
    const categoryData = analyticsData.reportsByCategory.map((item, index) => ({
        ...item,
        color: categoryColors[index % categoryColors.length]
    }));
    
    if (categoryData.length > 0) {
        const categoryCanvas = document.getElementById('categoryChart');
        const categoryLegend = document.getElementById('categoryLegend');
        
        const labels = categoryData.map(item => item.name);
        const values = categoryData.map(item => item.total);
        const bgColors = categoryData.map(item => item.color);
        
        new Chart(categoryCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-card').trim(),
                    cutout: '78%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 800,
                    easing: 'easeInOutQuart'
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
        
        // Create legend
        let legendHTML = '';
        categoryData.forEach((item, index) => {
            legendHTML += `
                <div class="legend-item">
                    <div class="legend-label-group">
                        <span class="legend-dot" style="background: ${item.color};"></span>
                        <span class="legend-label">${item.name}</span>
                    </div>
                    <span class="legend-count">${String(item.total).padStart(2, '0')}</span>
                </div>
            `;
        });
        categoryLegend.innerHTML = legendHTML;
    }
    
    // Severity Distribution
    const severityLabelMap = {
        critical: 'Critical',
        high: 'High',
        medium: 'Medium',
        low: 'Low'
    };
    createDonutChart('severityChart', analyticsData.severityDistribution, severityLabelMap, colors, 'severityLegend');
    
    // Verdict Breakdown
    const verdictLabelMap = {
        confirmed_threat: 'Confirmed Threat',
        false_positive: 'False Positive',
        escalated_externally: 'Escalated',
        pending: 'Pending'
    };
    createDonutChart('verdictChart', analyticsData.verdictBreakdown, verdictLabelMap, colors, 'verdictLegend');
    
    // Status Overview
    const statusLabelMap = {
        pending: 'Pending',
        under_review: 'Under Review',
        in_progress: 'In Progress',
        resolved: 'Resolved'
    };
    createDonutChart('statusChart', analyticsData.statusOverview, statusLabelMap, colors, 'statusLegend');
    
    // Reports Over Time - Line Chart
    if (analyticsData.reportsOverTime.length > 0) {
        const timelineCtx = document.getElementById('timelineChart');
        const dates = analyticsData.reportsOverTime.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const counts = analyticsData.reportsOverTime.map(item => item.total);
        
        new Chart(timelineCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Reports',
                    data: counts,
                    borderColor: '#34E4D6',
                    backgroundColor: function(context) {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
                        gradient.addColorStop(0, 'rgba(52, 228, 214, 0.25)');
                        gradient.addColorStop(1, 'rgba(52, 228, 214, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: counts.map(c => c > 0 ? 3 : 0),
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#34E4D6',
                    pointBorderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg').trim(),
                    pointBorderWidth: 2
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
                    x: {
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                            font: {
                                family: "'IBM Plex Mono', monospace",
                                size: 10
                            },
                            maxTicksLimit: 8
                        },
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--line-soft').trim(),
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                            font: {
                                family: "'IBM Plex Mono', monospace",
                                size: 10
                            },
                            stepSize: 1,
                            precision: 0
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
    
    // Resolution Time Trend - Line Chart
    if (analyticsData.resolutionTimeTrend.length > 0) {
        const resolutionCtx = document.getElementById('resolutionChart');
        const weeks = analyticsData.resolutionTimeTrend.map(item => item.week);
        const avgDays = analyticsData.resolutionTimeTrend.map(item => parseFloat(item.avg_days) || 0);
        
        new Chart(resolutionCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: weeks,
                datasets: [{
                    label: 'Avg Days',
                    data: avgDays,
                    borderColor: '#3DD68C',
                    backgroundColor: function(context) {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
                        gradient.addColorStop(0, 'rgba(61, 214, 140, 0.25)');
                        gradient.addColorStop(1, 'rgba(61, 214, 140, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: avgDays.map(d => d > 0 ? 3 : 0),
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#3DD68C',
                    pointBorderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg').trim(),
                    pointBorderWidth: 2
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
                    x: {
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                            font: {
                                family: "'IBM Plex Mono', monospace",
                                size: 10
                            }
                        },
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--line-soft').trim(),
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim(),
                            font: {
                                family: "'IBM Plex Mono', monospace",
                                size: 10
                            },
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
