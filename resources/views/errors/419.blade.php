<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - SentryDesk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0A1F1C;
            --bg-card: #102D27;
            --line: #1A3D35;
            --text: #E7F2F0;
            --text-dim: #8BA5A0;
            --cyan: #34E4D6;
            --amber: #F5B942;
            --amber-dim: rgba(245,185,66,0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
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
                linear-gradient(rgba(26,61,53,0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(26,61,53,0.3) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.5;
            pointer-events: none;
        }

        .error-container {
            max-width: 520px;
            width: 100%;
            background: var(--bg-card);
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 0 0 1px var(--line), 0 20px 60px rgba(0,0,0,0.5);
            text-align: center;
            position: relative;
            z-index: 1;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 24px;
            background: var(--amber-dim);
            border: 2px solid var(--amber);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: var(--amber);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .error-code {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 48px;
            font-weight: 700;
            color: var(--amber);
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .error-message {
            font-size: 15px;
            color: var(--text-dim);
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .error-details {
            background: rgba(245,185,66,0.08);
            border: 1px solid rgba(245,185,66,0.2);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 32px;
            text-align: left;
        }

        .error-details-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--amber);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-details-text {
            font-size: 13px;
            color: var(--text-dim);
            line-height: 1.5;
        }

        .error-details-text strong {
            color: var(--text);
        }

        .btn-back {
            display: inline-block;
            padding: 14px 32px;
            background: var(--cyan);
            color: #04211E;
            text-decoration: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            transition: opacity 0.2s, transform 0.2s;
        }

        .btn-back:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .footer-text {
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-dim);
        }

        .footer-text a {
            color: var(--cyan);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-clock-rotate-left"></i>
        </div>
        
        <div class="error-code">419</div>
        <h1 class="error-title">Session Expired</h1>
        <p class="error-message">
            Your session has timed out for security reasons. This usually happens when you leave a page open for too long.
        </p>

        <div class="error-details">
            <div class="error-details-title">
                <i class="fas fa-info-circle"></i>
                What happened?
            </div>
            <div class="error-details-text">
                For your security, login sessions expire after a period of inactivity. 
                This prevents unauthorized access if you forget to log out. 
                Simply <strong>log in again</strong> to continue.
            </div>
        </div>

        <a href="{{ route('login') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>

        <p class="footer-text">
            Need help? <a href="#">Contact Support</a>
        </p>
    </div>
</body>
</html>
