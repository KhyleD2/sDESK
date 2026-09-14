<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Generate 6-digit 2FA code
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Save code and expiration (10 minutes)
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();
            
            // Send email
            Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->name));
            
            // Log out temporarily until 2FA is verified
            Auth::logout();
            
            // Store email in session for 2FA verification
            $request->session()->put('2fa_email', $user->email);
            $request->session()->put('2fa_remember', $request->boolean('remember'));
            
            return redirect()->route('2fa.verify')->with('success', 'A 6-digit verification code has been sent to your email.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showTwoFactorForm()
    {
        if (!session()->has('2fa_email')) {
            return redirect()->route('login')->withErrors(['error' => 'Please login first.']);
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
        $user->save();

        // Log the user in
        Auth::login($user, session('2fa_remember', false));
        
        // Clear session data
        $request->session()->forget(['2fa_email', '2fa_remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Login successful!');
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

        return redirect()->route('login');
    }
}
