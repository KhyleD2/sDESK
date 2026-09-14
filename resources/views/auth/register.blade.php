@extends('layouts.app')

@section('title', 'Register — SentryDesk')

@section('content')
<!DOCTYPE html>
<html lang="en" id="authHtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — SentryDesk</title>
    <meta name="description" content="Create your SentryDesk account — Cybersecurity Incident Reporting & Response Management.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:          #0A1F1C;
            --bg-raised:   #0D2621;
            --bg-card:     #102D27;
            --bg-input:    #0D2621;
            --line:        #1A3D35;
            --line-soft:   #14342E;
            --text:        #E7F2F0;
            --text-dim:    #8BA5A0;
            --text-faint:  #6B847F;
            --cyan:        #34E4D6;
            --cyan-dim:    rgba(52,228,214,0.12);
            --amber:       #F5B942;
            --amber-dim:   rgba(245,185,66,0.12);
            --rose:        #FF5C7A;
            --rose-dim:    rgba(255,92,122,0.12);
            --violet:      #9B8CFF;
            --violet-dim:  rgba(155,140,255,0.12);
            --success:     #3DD68C;
            --success-dim: rgba(61,214,140,0.12);
        }

        [data-theme="light"] {
            --bg:          #E8EDEC;
            --bg-raised:   #FFFFFF;
            --bg-card:     #F3F7F6;
            --bg-input:    #EEF3F2;
            --line:        #C8D8D5;
            --line-soft:   #D8E5E3;
            --text:        #1F2937;
            --text-dim:    #374151;
            --text-faint:  #4B5563;
            --cyan:        #0891B2;
            --cyan-dim:    rgba(8,145,178,0.1);
            --amber:       #D97706;
            --amber-dim:   rgba(217,119,6,0.1);
            --rose:        #DC2626;
            --rose-dim:    rgba(220,38,38,0.1);
            --violet:      #7C3AED;
            --violet-dim:  rgba(124,58,237,0.1);
            --success:     #059669;
            --success-dim: rgba(5,150,105,0.1);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ── PAGE BACKGROUND ── */
        body {
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--line-soft) 1px, transparent 1px),
                linear-gradient(90deg, var(--line-soft) 1px, transparent 1px);
            background-size: 36px 36px;
            opacity: 0.35;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--cyan-dim) 0%, transparent 65%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        /* ── THEME TOGGLE ── */
        .auth-theme-toggle {
            position: fixed;
            top: 18px;
            right: 22px;
            z-index: 1000;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--bg-card);
            border: 1px solid var(--line);
            color: var(--text-dim);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .auth-theme-toggle:hover {
            background: var(--line-soft);
            color: var(--text);
        }

        /* ── CARD ── */
        .auth-card {
            display: flex;
            width: 1000px;
            max-width: 100%;
            border-radius: 18px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px var(--line),
                0 24px 80px rgba(0,0,0,0.55),
                0 8px 24px rgba(0,0,0,0.35);
            position: relative;
            z-index: 1;
            animation: cardIn 0.45s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(16px) scale(0.98); }
            to   { opacity: 1; transform: none; }
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            flex: 0 0 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-raised);
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 340px;
            height: 340px;
            background: radial-gradient(ellipse, var(--cyan-dim) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(52,228,214,0.05) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        [data-theme="light"] .auth-left::after {
            background-image: radial-gradient(circle, rgba(8,145,178,0.07) 1px, transparent 1px);
        }

        .left-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 44px 48px 64px;
            max-width: 380px;
        }

        /* ── LOGO ── */
        .logo-wrap {
            width: 96px;
            height: 96px;
            border-radius: 22px;
            background: var(--bg-card);
            border: 1.5px solid var(--cyan-dim);
            box-shadow:
                0 0 0 5px rgba(52,228,214,0.04),
                0 16px 48px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(52,228,214,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
            position: relative;
            transition: background-color 0.3s ease;
        }

        .logo-wrap::before {
            content: '';
            position: absolute;
            inset: -9px;
            border-radius: 30px;
            border: 1px solid var(--line);
            pointer-events: none;
        }

        .logo-icon-svg { width: 52px; height: 52px; }

        /* ── WORDMARK ── */
        .brand-wordmark {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1;
            margin-bottom: 10px;
        }
        .brand-wordmark .w-white { color: var(--text); }
        .brand-wordmark .w-cyan  { color: var(--cyan); }

        /* ── TAGLINE ── */
        .brand-tagline {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 9px;
            font-weight: 500;
            color: var(--text-faint);
            letter-spacing: 1.6px;
            text-transform: uppercase;
            line-height: 1.8;
            margin-bottom: 20px;
        }



        /* ── TICKER ── */
        .left-ticker {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            background: var(--bg-card);
            border-top: 1px solid var(--line-soft);
            display: flex;
            align-items: center;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .ticker-track {
            display: flex;
            gap: 36px;
            animation: tickerScroll 22s linear infinite;
            white-space: nowrap;
            padding-left: 100%;
        }

        @keyframes tickerScroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        .ticker-chip {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 8px;
            font-weight: 600;
            color: var(--text-faint);
            letter-spacing: 1px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ticker-chip span {
            display: inline-block;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 5px var(--cyan);
        }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 0 0 50%;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 44px 44px;
            overflow-y: auto;
            position: relative;
            transition: background-color 0.3s ease;
        }

        .auth-right::before {
            content: '';
            position: absolute;
            left: 0; top: 10%; bottom: 10%;
            width: 1px;
            background: linear-gradient(to bottom, transparent, var(--line) 30%, var(--line) 70%, transparent);
            opacity: 0.6;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 360px;
        }

        /* ── FORM HEADER ── */
        .form-heading {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.4px;
            margin-bottom: 4px;
        }

        .form-subtext {
            font-size: 13px;
            color: var(--text-dim);
            margin-bottom: 26px;
        }

        /* ── ERRORS ── */
        .form-errors {
            background: var(--rose-dim);
            border: 1px solid rgba(255,92,122,0.25);
            border-radius: 9px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }
        .form-errors p {
            font-size: 12px;
            color: var(--rose);
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .form-errors p + p { margin-top: 5px; }

        /* ── FIELDS ── */
        .field-group { margin-bottom: 16px; }

        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
        }

        .input-icon-wrap { position: relative; }

        .input-icon-wrap .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-faint);
            font-size: 12px;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            padding: 11px 13px 11px 38px;
            background: var(--bg-input);
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.3s ease;
        }
        .field-input::placeholder { color: var(--text-faint); }
        .field-input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px var(--cyan-dim);
        }
        .field-input.has-toggle { padding-right: 42px; }

        .pw-toggle {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-faint);
            cursor: pointer;
            font-size: 12px;
            padding: 4px;
            transition: color 0.18s;
            line-height: 1;
        }
        .pw-toggle:hover { color: var(--text-dim); }

        .field-error {
            font-size: 11px;
            color: var(--rose);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── SUBMIT ── */
        .btn-primary {
            width: 100%;
            padding: 12px 20px;
            background: var(--cyan);
            color: #04211E;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.1px;
            transition: all 0.18s ease;
            margin-top: 4px;
            margin-bottom: 18px;
        }
        .btn-primary:hover {
            opacity: 0.88;
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(52,228,214,0.28);
        }
        [data-theme="light"] .btn-primary:hover {
            box-shadow: 0 6px 22px rgba(8,145,178,0.25);
        }
        .btn-primary:active { transform: none; box-shadow: none; }

        /* ── OR DIVIDER ── */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .or-divider-line { flex: 1; height: 1px; background: var(--line); }
        .or-divider-text { font-size: 11px; color: var(--text-faint); }

        /* ── FOOTER LINK ── */
        .form-footer-link {
            text-align: center;
            font-size: 13px;
            color: var(--text-dim);
            margin-bottom: 22px;
        }
        .form-footer-link a {
            color: var(--cyan);
            text-decoration: none;
            font-weight: 600;
        }
        .form-footer-link a:hover { opacity: 0.8; }

        /* ── COPYRIGHT ── */
        .form-copyright {
            text-align: center;
            font-size: 11px;
            color: var(--text-faint);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 680px) {
            body { padding: 16px; align-items: flex-start; }
            .auth-card { flex-direction: column; width: 100%; }
            .auth-left { flex: none; min-height: auto; }
            .left-inner { padding: 32px 24px 52px; }
            .auth-right { flex: none; padding: 32px 24px; }
            .auth-right::before { display: none; }
            .auth-form-wrap { max-width: 100%; }
        }
    </style>
</head>
<body>

<!-- Theme Toggle -->
<button class="auth-theme-toggle" id="authThemeBtn" title="Toggle theme" aria-label="Toggle light/dark mode">
    <i class="fas fa-moon" id="authThemeIcon"></i>
</button>

<div class="auth-card">

    <!-- ══ LEFT: BRANDING ══ -->
    <div class="auth-left">
        <div class="left-inner">

            <!-- Logo icon -->
            <div class="logo-wrap">
                <svg class="logo-icon-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 6 L54 18.5 L54 45.5 L32 58 L10 45.5 L10 18.5 Z"
                          stroke="url(#hexGrad2)" stroke-width="1.5" fill="none" opacity="0.6"/>
                    <path d="M32 14 L46 20 L46 33 C46 41 39 47 32 50 C25 47 18 41 18 33 L18 20 Z"
                          fill="url(#shieldGrad2)" opacity="0.2"/>
                    <path d="M32 16 L44 21.5 L44 33 C44 39.5 38.5 45 32 47.5 C25.5 45 20 39.5 20 33 L20 21.5 Z"
                          stroke="url(#shieldStroke2)" stroke-width="1.5" fill="none"/>
                    <rect x="26" y="31" width="12" height="9" rx="2" fill="url(#lockGrad2)"/>
                    <path d="M27.5 31 L27.5 27.5 C27.5 24.5 36.5 24.5 36.5 27.5 L36.5 31"
                          stroke="url(#lockStroke2)" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                    <circle cx="32" cy="35.5" r="1.5" fill="#04211E"/>
                    <rect x="31.2" y="35.5" width="1.6" height="2.5" rx="0.5" fill="#04211E"/>
                    <defs>
                        <linearGradient id="hexGrad2" x1="10" y1="6" x2="54" y2="58" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#34E4D6"/>
                            <stop offset="100%" stop-color="#0FADA2"/>
                        </linearGradient>
                        <linearGradient id="shieldGrad2" x1="18" y1="14" x2="46" y2="50" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#34E4D6"/>
                            <stop offset="100%" stop-color="#0FADA2"/>
                        </linearGradient>
                        <linearGradient id="shieldStroke2" x1="18" y1="14" x2="46" y2="50" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#34E4D6"/>
                            <stop offset="100%" stop-color="#18C4B8"/>
                        </linearGradient>
                        <linearGradient id="lockGrad2" x1="26" y1="31" x2="38" y2="40" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#34E4D6"/>
                            <stop offset="100%" stop-color="#18C4B8"/>
                        </linearGradient>
                        <linearGradient id="lockStroke2" x1="27" y1="24" x2="37" y2="31" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#34E4D6"/>
                            <stop offset="100%" stop-color="#18C4B8"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <!-- Wordmark -->
            <div class="brand-wordmark">
                <span class="w-white">Sentry</span><span class="w-cyan">Desk</span>
            </div>

            <!-- Tagline -->
            <p class="brand-tagline">
                Cybersecurity Incident Reporting &amp;<br>Response Management
            </p>


        </div>

        <!-- Ticker strip -->
        <div class="left-ticker">
            <div class="ticker-track">
                <div class="ticker-chip"><span></span> Threat Intel Active</div>
                <div class="ticker-chip"><span></span> Incident Response Ready</div>
                <div class="ticker-chip"><span></span> SOC Platform</div>
                <div class="ticker-chip"><span></span> Secure Reporting</div>
                <div class="ticker-chip"><span></span> 24/7 Monitoring</div>
                <div class="ticker-chip"><span></span> Threat Intel Active</div>
                <div class="ticker-chip"><span></span> Incident Response Ready</div>
                <div class="ticker-chip"><span></span> SOC Platform</div>
                <div class="ticker-chip"><span></span> Secure Reporting</div>
                <div class="ticker-chip"><span></span> 24/7 Monitoring</div>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT: FORM ══ -->
    <div class="auth-right">
        <div class="auth-form-wrap">

            <h1 class="form-heading">Create your account</h1>
            <p class="form-subtext">Join SentryDesk and start securing your organization</p>

            @if ($errors->any())
                <div class="form-errors">
                    @foreach ($errors->all() as $error)
                        <p><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="field-group">
                    <label for="name" class="field-label">Full Name</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input
                            type="text" id="name" name="name"
                            value="{{ old('name') }}"
                            required autofocus autocomplete="name"
                            placeholder="Enter your full name"
                            class="field-input"
                        >
                    </div>
                    @error('name')
                        <span class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="field-group">
                    <label for="email" class="field-label">Email</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input
                            type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            required autocomplete="email"
                            placeholder="you@example.com"
                            class="field-input"
                        >
                    </div>
                    @error('email')
                        <span class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Role: locked to User only — hidden input -->
                <input type="hidden" name="role" value="user">

                <!-- Password -->
                <div class="field-group">
                    <label for="password" class="field-label">Password</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input
                            type="password" id="password" name="password"
                            required autocomplete="new-password"
                            placeholder="Create a password"
                            class="field-input has-toggle"
                        >
                        <button type="button" class="pw-toggle" id="pwToggle1" aria-label="Toggle password">
                            <i class="fas fa-eye" id="pwIcon1"></i>
                        </button>
                    </div>
                    <div style="font-size: 11px; color: var(--text-dim); margin-top: 6px; line-height: 1.4;">
                        <i class="fas fa-info-circle"></i> Must be at least 8 characters with one special character (!@#$%^&*...)
                    </div>
                    @error('password')
                        <span class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="field-group">
                    <label for="password_confirmation" class="field-label">Confirm Password</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input
                            type="password" id="password_confirmation" name="password_confirmation"
                            required autocomplete="new-password"
                            placeholder="Confirm your password"
                            class="field-input has-toggle"
                        >
                        <button type="button" class="pw-toggle" id="pwToggle2" aria-label="Toggle confirm password">
                            <i class="fas fa-eye" id="pwIcon2"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary">Register</button>

                <!-- OR -->
                <div class="or-divider">
                    <span class="or-divider-line"></span>
                    <span class="or-divider-text">or</span>
                    <span class="or-divider-line"></span>
                </div>

                <!-- Login link -->
                <p class="form-footer-link">
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </p>
            </form>

            <p class="form-copyright">© 2026 SentryDesk — Information Assurance &amp; Security</p>
        </div>
    </div>
</div>

<script>
(function(){
    function togglePw(toggleId, inputId, iconId){
        var t = document.getElementById(toggleId);
        var i = document.getElementById(inputId);
        var ic = document.getElementById(iconId);
        if(t && i){ t.addEventListener('click', function(){
            var h = i.type==='password';
            i.type = h?'text':'password';
            ic.className = h?'fas fa-eye-slash':'fas fa-eye';
        }); }
    }
    togglePw('pwToggle1','password','pwIcon1');
    togglePw('pwToggle2','password_confirmation','pwIcon2');

    var html = document.getElementById('authHtml');
    var btn  = document.getElementById('authThemeBtn');
    var icon = document.getElementById('authThemeIcon');

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        icon.className = theme === 'light' ? 'fas fa-sun' : 'fas fa-moon';
    }

    var saved = localStorage.getItem('theme') || 'dark';
    applyTheme(saved);

    btn.addEventListener('click', function(){
        var current = html.getAttribute('data-theme');
        var next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    });
})();
</script>
</body>
</html>
@endsection
