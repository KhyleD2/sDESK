<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your 2FA Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Arial', sans-serif; background-color: #0A1F1C;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #0A1F1C; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #102D27; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0D2621 0%, #102D27 100%); padding: 30px; text-align: center; border-bottom: 2px solid #34E4D6;">
                            <h1 style="margin: 0; color: #34E4D6; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                🔐 SentryDesk
                            </h1>
                            <p style="margin: 8px 0 0; color: #8BA5A0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                Two-Factor Authentication
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px; color: #E7F2F0; font-size: 16px;">
                                Hello <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="margin: 0 0 30px; color: #8BA5A0; font-size: 14px; line-height: 1.6;">
                                You've requested to sign in to your SentryDesk account. Please use the verification code below to complete your login:
                            </p>
                            
                            <!-- Code Box -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <div style="display: inline-block; background: #0D2621; border: 2px solid #34E4D6; border-radius: 12px; padding: 20px 40px;">
                                            <p style="margin: 0; color: #8BA5A0; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                                                Your Verification Code
                                            </p>
                                            <p style="margin: 0; color: #34E4D6; font-size: 36px; font-weight: 700; font-family: 'Courier New', monospace; letter-spacing: 8px;">
                                                {{ $code }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Warning -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(255,92,122,0.1); border-left: 3px solid #FF5C7A; border-radius: 6px; margin-top: 30px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0; color: #FF5C7A; font-size: 13px; font-weight: 600;">
                                            ⚠️ Security Notice
                                        </p>
                                        <p style="margin: 8px 0 0; color: #8BA5A0; font-size: 12px; line-height: 1.5;">
                                            This code will expire in <strong>10 minutes</strong>. Never share this code with anyone. If you didn't request this code, please ignore this email or contact your administrator.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0D2621; padding: 20px 30px; text-align: center; border-top: 1px solid #1A3D35;">
                            <p style="margin: 0; color: #6B847F; font-size: 11px;">
                                © 2026 SentryDesk — Information Assurance & Security
                            </p>
                            <p style="margin: 8px 0 0; color: #6B847F; font-size: 11px;">
                                This is an automated email. Please do not reply.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
