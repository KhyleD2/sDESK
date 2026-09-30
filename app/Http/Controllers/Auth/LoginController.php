<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        // Check if user is blocked by admin
        if ($user && $user->is_blocked) {
            $this->logLoginAttempt($user->id, $credentials['email'], $request, 'failed', 'Account blocked by administrator');
            return back()->withErrors([
                'email' => 'Your account has been blocked. Please contact the administrator.',
            ])->onlyInput('email');
        }

        // Check if user is temporarily locked due to failed attempts
        if ($user && $user->locked_until && $user->locked_until > now()) {
            $minutesLeft = now()->diffInMinutes($user->locked_until);
            $this->logLoginAttempt($user->id, $credentials['email'], $request, 'failed', 'Account temporarily locked');
            return back()->withErrors([
                'email' => "Too many failed login attempts. Account locked for {$minutesLeft} more minute(s).",
            ])->onlyInput('email');
        }

        // Reset lockout if time has passed
        if ($user && $user->locked_until && $user->locked_until <= now()) {
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
            $user->save();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Reset failed attempts on successful login
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
            $user->save();
            
            // Skip 2FA for admin and analyst roles only
            if (in_array($user->role, ['admin', 'analyst'])) {
                $this->logLoginAttempt($user->id, $user->email, $request, 'success', '2FA skipped (admin/analyst)');
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
            }
            
            // Check if device is trusted
            $deviceToken = $request->cookie('trusted_device');
            if ($deviceToken && $user->trusted_device_token === $deviceToken && $user->trusted_device_expires_at > now()) {
                // Device is trusted, skip 2FA
                $this->logLoginAttempt($user->id, $user->email, $request, 'success');
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
            }
            
            // Generate 6-digit 2FA code
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Save code and expiration (10 minutes)
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();
            
            // Send email
            Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->name));
            
            // Log successful password verification (2FA pending)
            $this->logLoginAttempt($user->id, $user->email, $request, 'success', '2FA code sent');
            
            // Log out temporarily until 2FA is verified
            Auth::logout();
            
            // Store email in session for 2FA verification
            $request->session()->put('2fa_email', $user->email);
            $request->session()->put('2fa_remember', $request->boolean('remember'));
            $request->session()->save(); // Force save session
            
            return redirect()->route('2fa.verify')->with('success', 'A 6-digit verification code has been sent to your email.');
        }

        // Failed login attempt
        if ($user) {
            $user->failed_login_attempts += 1;
            
            // Lock account after 5 failed attempts for 15 minutes
            if ($user->failed_login_attempts >= 5) {
                $user->locked_until = now()->addMinutes(15);
                $user->save();
                $this->logLoginAttempt($user->id, $credentials['email'], $request, 'failed', 'Invalid credentials - Account locked after 5 attempts');
                return back()->withErrors([
                    'email' => 'Too many failed login attempts. Your account has been locked for 15 minutes.',
                ])->onlyInput('email');
            }
            
            $user->save();
            $attemptsLeft = 5 - $user->failed_login_attempts;
            $this->logLoginAttempt($user->id, $credentials['email'], $request, 'failed', 'Invalid credentials');
            return back()->withErrors([
                'email' => "Invalid credentials. {$attemptsLeft} attempt(s) remaining before lockout.",
            ])->onlyInput('email');
        }

        // User not found
        $this->logLoginAttempt(null, $credentials['email'], $request, 'failed', 'User not found');
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    protected function logLoginAttempt($userId, $email, $request, $status, $failureReason = null)
    {
        LoginLog::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $status,
            'failure_reason' => $failureReason,
        ]);
    }

    public function showTwoFactorForm()
    {
        if (!session()->has('2fa_email')) {
            return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
        }
        
        // Clear any existing auth to prevent conflicts
        if (Auth::check()) {
            Auth::logout();
        }
        
        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $email = session('2fa_email');
        
        if (!$email) {
            return back()->withErrors(['code' => 'Session expired. Please login again.']);
        }

        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['code' => 'User not found.']);
        }

        // Check if code matches and hasn't expired
        if ($user->two_factor_code !== $request->code) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        if ($user->two_factor_expires_at < now()) {
            return back()->withErrors(['code' => 'Verification code has expired. Please login again.']);
        }

        // Clear 2FA code
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        
        // Handle "Trust this device" option
        $response = redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
        
        if ($request->boolean('trust_device')) {
            $deviceToken = Str::random(60);
            $user->trusted_device_token = $deviceToken;
            $user->trusted_device_expires_at = now()->addDays(7);
            
            // Set cookie for 7 days (minutes * hours * days)
            $response->withCookie(cookie('trusted_device', $deviceToken, 60 * 24 * 7, '/', null, false, true));
        }
        
        $user->save();

        // Log the user in
        Auth::login($user, session('2fa_remember', false));
        
        // Clear session data and regenerate to prevent conflicts
        $request->session()->forget(['2fa_email', '2fa_remember']);
        $request->session()->regenerate();

        return $response;
    }

    public function resendTwoFactorCode(Request $request)
    {
        $email = session('2fa_email');
        
        if (!$email) {
            return back()->withErrors(['code' => 'Session expired. Please login again.']);
        }

        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['code' => 'User not found.']);
        }

        // Generate new 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Save code and expiration (10 minutes)
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();
        
        // Send email
        Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->name));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flush(); // Clear all session data

        return redirect()->route('login');
    }
}
