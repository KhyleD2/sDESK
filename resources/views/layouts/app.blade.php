<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SentryDesk')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg: #0A1F1C;
            --bg-raised: #0D2621;
            --bg-card: #102D27;
            --line: #1A3D35;
            --line-soft: #14342E;
            --text: #E7F2F0;
            --text-dim: #8BA5A0;
            --text-faint: #6B847F;
            --cyan: #34E4D6;
            --cyan-dim: rgba(52,228,214,0.12);
            --amber: #F5B942;
            --amber-dim: rgba(245,185,66,0.12);
            --rose: #FF5C7A;
            --rose-dim: rgba(255,92,122,0.12);
            --violet: #9B8CFF;
            --violet-dim: rgba(155,140,255,0.12);
            --success: #3DD68C;
            --success-dim: rgba(61,214,140,0.12);
            --medium: #E9D566;
        }
        
        [data-theme="light"] {
            --bg: #F5F7F9;
            --bg-raised: #FFFFFF;
            --bg-card: #FFFFFF;
            --line: #E1E4E8;
            --line-soft: #D0D7DE;
            --text: #1F2937;
            --text-dim: #374151;
            --text-faint: #4B5563;
            --cyan: #0891B2;
            --cyan-dim: rgba(8,145,178,0.1);
            --amber: #D97706;
            --amber-dim: rgba(217,119,6,0.1);
            --rose: #DC2626;
            --rose-dim: rgba(220,38,38,0.1);
            --violet: #7C3AED;
            --violet-dim: rgba(124,58,237,0.1);
            --success: #059669;
            --success-dim: rgba(5,150,105,0.1);
            --medium: #CA8A04;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
            /* Disable slashed/dotted zero glyphs globally */
            font-feature-settings: 'zero' 0;
            font-variant-numeric: normal;
        }
        
        /* Monospace for IDs, timestamps, counts — uses Inter to avoid dotted zeros */
        .mono, .stat-value, .chart-number, .timestamp, .count, .id, .hash {
            font-family: 'Inter', sans-serif !important;
            font-feature-settings: 'zero' 0;
            font-variant-numeric: normal;
        }
        
        /* Layout */
        .layout-container { display: flex; min-height: 100vh; }
        
        /* === SIDEBAR === */
        .sidebar {
            width: 260px;
            background: var(--bg-raised);
            border-right: 1px solid var(--line-soft);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 200;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        .sidebar-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line-soft);
            transition: border-color 0.3s ease;
        }
        
        .sidebar-brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .sidebar-logo {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--cyan);
        }
        
        .sidebar-brand {
            font-size: 19px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.5px;
        }
        
        .sidebar-subtitle {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            color: var(--text-faint);
            letter-spacing: 1px;
            padding-left: 50px;
        }
        
        .sidebar-section {
            padding: 20px 16px 12px 16px;
        }
        
        .sidebar-section-title {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-faint);
            font-weight: 600;
            margin-bottom: 10px;
            padding-left: 12px;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-nav li {
            margin-bottom: 2px;
            position: relative;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 12px;
            color: var(--text-dim);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 13.5px;
            font-weight: 500;
            position: relative;
        }
        
        .sidebar-nav a:hover {
            background: var(--line-soft);
            color: var(--text);
        }
        
        .sidebar-nav a.active {
            background: var(--cyan-dim);
            color: var(--cyan);
        }
        
        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 22px;
            background: var(--cyan);
            border-radius: 0 2px 2px 0;
        }
        
        .sidebar-nav-icon {
            width: 16px;
            font-size: 15px;
            text-align: center;
        }
        
        .sidebar-nav-badge {
            margin-left: auto;
            background: var(--rose-dim);
            color: var(--rose);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            line-height: 1.4;
        }
        
        .sidebar-footer {
            margin-top: auto;
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.03);
        }
        
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px;
            background: var(--bg);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        [data-theme="light"] .sidebar-user {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        
        .sidebar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--cyan-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: var(--cyan);
            border: 2px solid var(--cyan);
        }
        
        .sidebar-user-info {
            flex: 1;
        }
        
        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
        }
        
        .sidebar-user-role {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            color: var(--cyan);
            font-weight: 500;
        }
        
        .sidebar-user-role::before {
            content: '● ';
        }
        
        /* === MAIN CONTENT === */
        .main-content {
            flex: 1;
            margin-left: 260px;
            background: var(--bg);
            min-height: 100vh;
        }
        
        /* === STATUS TICKER === */
        .status-ticker {
            height: 34px;
            background: var(--bg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            display: flex;
            align-items: center;
            padding: 0 32px;
            gap: 24px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--text-faint);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .ticker-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .ticker-divider {
            color: var(--line);
        }
        
        .ticker-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 8px var(--cyan);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }
        
        .ticker-status-ok {
            color: var(--cyan);
        }
        
        .ticker-count-high {
            color: var(--rose);
        }
        
        /* === TOPBAR === */
        .top-bar {
            height: 54px;
            border-bottom: 1px solid var(--line-soft);
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: var(--bg);
            z-index: 99;
        }
        
        .page-context-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.2px;
        }
        
        .top-bar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: var(--bg-card);
            color: var(--text-dim);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.2s ease;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border: none;
        }
        
        [data-theme="light"] .icon-btn {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        
        .icon-btn:hover {
            background: var(--line-soft);
            color: var(--text);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        
        [data-theme="light"] .icon-btn:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .notification-badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 7px;
            height: 7px;
            background: var(--rose);
            border-radius: 50%;
            border: 2px solid var(--bg-card);
        }
        
        .logout-btn {
            background: var(--rose-dim);
            color: var(--rose);
            border: none;
            padding: 10px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        [data-theme="light"] .logout-btn {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        
        .logout-btn:hover {
            background: rgba(255,92,122,0.18);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        
        [data-theme="light"] .logout-btn:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* === TOPBAR PROFILE CHIP === */
        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 12px 5px 5px;
            background: var(--bg-card);
            border-radius: 99px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            cursor: default;
            transition: background 0.2s;
        }

        [data-theme="light"] .topbar-profile {
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .topbar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--cyan-dim);
            border: 2px solid var(--cyan);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--cyan);
            flex-shrink: 0;
        }

        .topbar-profile-info {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .topbar-profile-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .topbar-profile-role {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            font-weight: 600;
            color: var(--cyan);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* === SIDEBAR LOGOUT === */
        .sidebar-logout-btn {
            width: 100%;
            background: var(--rose-dim);
            color: var(--rose);
            border: none;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.1px;
        }

        .sidebar-logout-btn:hover {
            background: rgba(255,92,122,0.22);
        }
        
        /* === CONTENT AREA === */
        .content-area {
            padding: 40px 80px;
            max-width: 1800px;
        }
        
        /* === NOTIFICATION DROPDOWN === */
        .notification-bell-container {
            position: relative;
        }
        
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 400px;
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            display: none;
            z-index: 1000;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }
        
        [data-theme="light"] .notification-dropdown {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.08);
        }
        
        .notification-dropdown.show {
            display: block;
            animation: slideDown 0.25s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .notification-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification-header h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }
        
        .notification-mark-all {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            color: var(--cyan);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notification-mark-all:hover {
            color: #5AEEE3;
        }
        
        .notification-list {
            max-height: 420px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        
        .notification-item:hover {
            background: var(--line-soft);
        }
        
        .notification-item.unread {
            background: var(--cyan-dim);
            border-left: 3px solid var(--cyan);
            padding-left: 17px;
        }
        
        .notification-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        
        .notification-icon.icon-new-report {
            background: var(--rose-dim);
            color: var(--rose);
        }
        
        .notification-icon.icon-status-change {
            background: var(--violet-dim);
            color: var(--violet);
        }
        
        .notification-icon.icon-resolved {
            background: var(--success-dim);
            color: var(--success);
        }
        
        .notification-icon.icon-assigned {
            background: var(--cyan-dim);
            color: var(--cyan);
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-message {
            font-size: 13px;
            color: var(--text);
            line-height: 1.5;
            margin-bottom: 5px;
        }
        
        .notification-time {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            color: var(--text-faint);
            text-transform: uppercase;
        }
        
        .notification-empty {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-faint);
        }
        
        .notification-empty i {
            font-size: 36px;
            margin-bottom: 14px;
            opacity: 0.3;
        }
        
        /* Flash Messages */
        .flash-message {
            padding: 14px 20px;
            margin-bottom: 24px;
            border-radius: 10px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .flash-success {
            background: var(--success-dim);
            color: var(--success);
        }
        
        .flash-error {
            background: var(--rose-dim);
            color: var(--rose);
        }
        
        /* Chart.js Global Defaults */
        @media screen {
            :root {
                --chart-font: 'IBM Plex Mono', monospace;
            }
        }
    </style>
</head>
<body>
    @auth
    <div class="layout-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand-row">
                    <div class="sidebar-logo"><i class="fas fa-shield-alt"></i></div>
                    <span class="sidebar-brand">SentryDesk</span>
                </div>
            </div>
            
            @if(Auth::user()->isUser())
                {{-- USER Sidebar --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">MAIN</div>
                    <ul class="sidebar-nav">
                        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line sidebar-nav-icon"></i> Dashboard
                        </a></li>
                        <li><a href="{{ route('reports.create') }}" class="{{ request()->routeIs('reports.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle sidebar-nav-icon"></i> Submit Report
                        </a></li>
                        <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">
                            <i class="fas fa-file-alt sidebar-nav-icon"></i> My Reports
                        </a></li>
                    </ul>
                </div>
            @elseif(Auth::user()->isAnalyst())
                {{-- ANALYST Sidebar --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">MAIN</div>
                    <ul class="sidebar-nav">
                        <li><a href="{{ route('analyst.dashboard') }}" class="{{ request()->routeIs('analyst.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line sidebar-nav-icon"></i> Dashboard
                        </a></li>
                        <li><a href="{{ route('analyst.report-queue.index') }}" class="{{ request()->routeIs('analyst.report-queue.*') ? 'active' : '' }}">
                            <i class="fas fa-list-alt sidebar-nav-icon"></i> Report Queue
                        </a></li>
                        <li><a href="{{ route('analyst.known-threats') }}" class="{{ request()->routeIs('analyst.known-threats') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle sidebar-nav-icon"></i> Known Threats
                        </a></li>
                        <li><a href="{{ route('analyst.activity-logs') }}" class="{{ request()->routeIs('analyst.activity-logs') ? 'active' : '' }}">
                            <i class="fas fa-history sidebar-nav-icon"></i> Activity Logs
                        </a></li>
                    </ul>
                </div>
            @elseif(Auth::user()->isAdmin())
                {{-- ADMIN Sidebar --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">MAIN</div>
                    <ul class="sidebar-nav">
                        <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line sidebar-nav-icon"></i> Dashboard
                        </a></li>
                        <li><a href="{{ route('admin.report-queue.index') }}" class="{{ request()->routeIs('admin.report-queue.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list sidebar-nav-icon"></i> All Reports
                        </a></li>
                        <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users sidebar-nav-icon"></i> User Management
                        </a></li>
                        <li><a href="{{ route('admin.known-threats') }}" class="{{ request()->routeIs('admin.known-threats') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle sidebar-nav-icon"></i> Known Threats
                        </a></li>
                        <li><a href="{{ route('admin.activity-logs') }}" class="{{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                            <i class="fas fa-history sidebar-nav-icon"></i> Activity Logs
                        </a></li>
                        <li><a href="{{ route('admin.analytics') }}" class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar sidebar-nav-icon"></i> Analytics
                        </a></li>
                    </ul>
                </div>
                <div class="sidebar-section">
                    <div class="sidebar-section-title">SYSTEM</div>
                    <ul class="sidebar-nav">
                        <li><a href="{{ route('admin.login-logs') }}" class="{{ request()->routeIs('admin.login-logs') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt sidebar-nav-icon"></i> Login Logs
                        </a></li>
                        <li><a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            <i class="fas fa-cog sidebar-nav-icon"></i> Settings
                        </a></li>
                    </ul>
                </div>
            @endif
            
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="page-context-title">
                    @yield('page-context-title', 'Command Overview')
                </div>
                <div class="top-bar-actions">
                    <!-- Notification Bell -->
                    <div class="notification-bell-container">
                        <button class="icon-btn" id="notificationBellBtn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge-dot" id="notificationBadge" style="display: none;"></span>
                        </button>
                        
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <h3>Notifications</h3>
                                <a href="#" class="notification-mark-all" id="markAllReadBtn" style="display: none;">Mark All Read</a>
                            </div>
                            <div class="notification-list" id="notificationList">
                                <div class="notification-empty">
                                    <i class="fas fa-bell"></i>
                                    <div>No notifications</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="icon-btn" id="themeToggleBtn" title="Toggle Theme">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>

                    {{-- Profile chip --}}
                    <div class="topbar-profile">
                        <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        <div class="topbar-profile-info">
                            <span class="topbar-profile-name">{{ Auth::user()->name }}</span>
                            <span class="topbar-profile-role">{{ strtoupper(Auth::user()->role) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                @if(session('success'))
                    <div class="flash-message flash-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="flash-message flash-error">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    @else
        {{-- Guest users (login/register pages) --}}
        @yield('content')
    @endauth
    
    @auth
    <script>
        // Theme Toggle Functionality
        (function() {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;
            
            // Load saved theme from localStorage or default to dark
            const savedTheme = localStorage.getItem('theme') || 'dark';
            html.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
            
            themeToggleBtn.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });
            
            function updateThemeIcon(theme) {
                if (theme === 'light') {
                    themeIcon.className = 'fas fa-sun';
                } else {
                    themeIcon.className = 'fas fa-moon';
                }
            }
        })();
        
        // Set Chart.js global defaults
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = "'IBM Plex Mono', monospace";
            Chart.defaults.color = '#7C8698';
            Chart.defaults.borderColor = '#1A2029';
        }
        
        // Notification Dropdown Functionality
        (function() {
            const bellBtn = document.getElementById('notificationBellBtn');
            const dropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            
            let isDropdownOpen = false;
            
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                isDropdownOpen = !isDropdownOpen;
                
                if (isDropdownOpen) {
                    dropdown.classList.add('show');
                    loadNotifications();
                } else {
                    dropdown.classList.remove('show');
                }
            });
            
            document.addEventListener('click', function(e) {
                if (isDropdownOpen && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                    isDropdownOpen = false;
                }
            });
            
            function loadNotifications() {
                fetch('{{ route("api.notifications.recent") }}?limit=10')
                    .then(response => response.json())
                    .then(data => {
                        updateBadge(data.unread_count);
                        renderNotifications(data.notifications);
                    })
                    .catch(error => console.error('Error loading notifications:', error));
            }
            
            function updateBadge(count) {
                if (count > 0) {
                    notificationBadge.style.display = 'block';
                    markAllReadBtn.style.display = 'inline-block';
                } else {
                    notificationBadge.style.display = 'none';
                    markAllReadBtn.style.display = 'none';
                }
            }
            
            function renderNotifications(notifications) {
                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="notification-empty">
                            <i class="fas fa-bell"></i>
                            <div>No notifications</div>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                notifications.forEach(notification => {
                    const icon = getNotificationIcon(notification.message);
                    const unreadClass = notification.is_read ? '' : 'unread';
                    
                    html += `
                        <div class="notification-item ${unreadClass}" data-id="${notification.id}" data-url="${notification.report_url || '#'}">
                            <div class="notification-icon ${icon.class}">
                                <i class="${icon.icon}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-message">${notification.message}</div>
                                <div class="notification-time">${notification.created_at}</div>
                            </div>
                        </div>
                    `;
                });
                
                notificationList.innerHTML = html;
                
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const notificationId = this.dataset.id;
                        const url = this.dataset.url;
                        
                        markAsRead(notificationId, () => {
                            if (url && url !== '#') {
                                window.location.href = url;
                            }
                        });
                    });
                });
            }
            
            function getNotificationIcon(message) {
                const lowerMessage = message.toLowerCase();
                
                if (lowerMessage.includes('resolved') || lowerMessage.includes('closed')) {
                    return { icon: 'fas fa-check-circle', class: 'icon-resolved' };
                } else if (lowerMessage.includes('assigned')) {
                    return { icon: 'fas fa-user-check', class: 'icon-assigned' };
                } else if (lowerMessage.includes('status') || lowerMessage.includes('updated')) {
                    return { icon: 'fas fa-sync-alt', class: 'icon-status-change' };
                } else {
                    return { icon: 'fas fa-exclamation-triangle', class: 'icon-new-report' };
                }
            }
            
            function markAsRead(notificationId, callback) {
                fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        if (callback) callback();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            }
            
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                fetch('{{ route("api.notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                    }
                })
                .catch(error => console.error('Error marking all as read:', error));
            });
            
            loadNotifications();
            
            setInterval(function() {
                if (!isDropdownOpen) {
                    fetch('{{ route("api.notifications.recent") }}?limit=1')
                        .then(response => response.json())
                        .then(data => {
                            updateBadge(data.unread_count);
                        })
                        .catch(error => console.error('Error polling notifications:', error));
                }
            }, 30000);
        })();
    </script>
    @endauth
</body>
</html>
