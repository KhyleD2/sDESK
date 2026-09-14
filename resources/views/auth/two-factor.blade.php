<!DOCTYPE html>
<html lang="en" id="authHtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification — SentryDesk</title>
    <meta name="description" content="Two-Factor Authentication Verification">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            --success:     #059669;
            --success-dim: rgba(5,150,105,0.1);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

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

        .twofa-card {
            width: 480px;
            max-width: 100%;
            background: var(--bg-card);
            border-radius: 18px;
            padding: 44px;
            box-shadow:
                0 0 0 1px var(--line),
                0 24px 80px rgba(0,0,0,0.55),
                0 8px 24px rgba(0,0,0,0.35);
            position: relative;
            z-index: 1;
            animation: cardIn 0.45s cubic-bezier(0.22,1,0.36,1) both;
            text-align: center;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(16px) scale(0.98); }
            to   { opacity: 1; transform: none; }
        }

        .shield-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: var(--cyan-dim);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--cyan);
            border: 2px solid var(--cyan);
        }

        .form-heading {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: -0.4px;
        }

        .form-subtext {
            font-size: 14px;
            color: var(--text-dim);
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .success-alert {
            background: var(--success-dim);
            border: 1px solid rgba(61,214,140,0.25);
            border-radius: 9px;
            padding: 12px 16px;
            margin-bottom: 20px;
            text-align: left;
        }
        .success-alert p {
            font-size: 12px;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0;
        }

        .form-errors {
            background: var(--rose-dim);
            border: 1px solid rgba(255,92,122,0.25);
            border-radius: 9px;
            padding: 12px 16px;
            margin-bottom: 20px;
            text-align: left;
        }
        .form-errors p {
            font-size: 12px;
            color: var(--rose);
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0;
        }

        .code-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .code-digit {
            width: 56px;
            height: 64px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            background: var(--bg-input);
            border: 2px solid var(--line);
            border-radius: 10px;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .code-digit:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px var(--cyan-dim);
        }

        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: var(--cyan);
            color: #04211E;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
            margin-bottom: 16px;
        }

        .btn-primary:hover {
            opacity: 0.88;
            transform: translateY(-1px);
        }

        .resend-link {
            font-size: 13px;
            color: var(--text-dim);
            margin-bottom: 20px;
        }

        .resend-link button {
            background: none;
            border: none;
            color: var(--cyan);
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
            font-size: 13px;
        }

        .resend-link button:hover {
            opacity: 0.8;
        }

        .back-link {
            font-size: 13px;
            color: var(--cyan);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<button class="auth-theme-toggle" id="authThemeBtn" title="Toggle theme">
    <i class="fas fa-moon" id="authThemeIcon"></i>
</button>

<div class="twofa-card">
    <div class="shield-icon">
        <i class="fas fa-shield-alt"></i>
    </div>

    <h1 class="form-heading">Two-Factor Authentication</h1>
    <p class="form-subtext">
        We've sent a 6-digit verification code to your email.<br>
        Please enter it below to complete your login.
    </p>

    @if (session('success'))
        <div class="success-alert">
            <p><i class="fas fa-check-circle"></i> {{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="form-errors">
            @foreach ($errors->all() as $error)
                <p><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('2fa.verify') }}" id="twoFaForm">
        @csrf
        
        <input type="hidden" name="code" id="hiddenCode">
        
        <div class="code-input-group">
            <input type="text" maxlength="1" class="code-digit" id="digit1" autocomplete="off" autofocus>
            <input type="text" maxlength="1" class="code-digit" id="digit2" autocomplete="off">
            <input type="text" maxlength="1" class="code-digit" id="digit3" autocomplete="off">
            <input type="text" maxlength="1" class="code-digit" id="digit4" autocomplete="off">
            <input type="text" maxlength="1" class="code-digit" id="digit5" autocomplete="off">
            <input type="text" maxlength="1" class="code-digit" id="digit6" autocomplete="off">
        </div>

        <button type="submit" class="btn-primary">Verify & Login</button>
    </form>

    <form method="POST" action="{{ route('2fa.resend') }}" class="resend-link">
        @csrf
        <p>Didn't receive the code? <button type="submit">Resend Code</button></p>
    </form>

    <a href="{{ route('login') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Login
    </a>
</div>

<script>
(function(){
    // Theme toggle
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

    // Code input handling
    var digits = [
        document.getElementById('digit1'),
        document.getElementById('digit2'),
        document.getElementById('digit3'),
        document.getElementById('digit4'),
        document.getElementById('digit5'),
        document.getElementById('digit6')
    ];

    digits.forEach(function(digit, index) {
        digit.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length === 1 && index < 5) {
                digits[index + 1].focus();
            }
            
            // Update hidden field
            updateHiddenCode();
        });

        digit.addEventListener('keydown', function(e) {
            // Backspace on empty field goes to previous
            if (e.key === 'Backspace' && !this.value && index > 0) {
                digits[index - 1].focus();
            }
            
            // Arrow keys navigation
            if (e.key === 'ArrowLeft' && index > 0) {
                digits[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < 5) {
                digits[index + 1].focus();
            }
        });

        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            var pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            
            for (var i = 0; i < pastedData.length && (index + i) < 6; i++) {
                digits[index + i].value = pastedData[i];
            }
            
            var nextIndex = Math.min(index + pastedData.length, 5);
            digits[nextIndex].focus();
            
            updateHiddenCode();
        });
    });

    function updateHiddenCode() {
        var code = '';
        digits.forEach(function(digit) {
            code += digit.value;
        });
        document.getElementById('hiddenCode').value = code;
    }

    // Auto-submit when all 6 digits are entered
    digits[5].addEventListener('input', function() {
        if (this.value.length === 1) {
            updateHiddenCode();
            // Small delay for better UX
            setTimeout(function() {
                document.getElementById('twoFaForm').submit();
            }, 300);
        }
    });
})();
</script>
</body>
</html>
