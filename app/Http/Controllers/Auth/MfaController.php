<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MfaOtpMail;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MfaController extends Controller
{
    public function show()
    {
        if (!session('mfa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.mfa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('mfa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        // Check expiry
        if (!$user->mfa_code_expires_at || now()->isAfter($user->mfa_code_expires_at)) {
            return back()->withErrors(['code' => 'Your verification code has expired. Please log in again.']);
        }

        // Check code
        if ($request->code !== $user->mfa_code) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Clear OTP
        $user->update([
            'mfa_code'            => null,
            'mfa_code_expires_at' => null,
            'last_login_at'       => now(),
            'last_login_ip'       => $request->ip(),
        ]);

        // Log in the user
        Auth::login($user, session('mfa_remember', false));
        session()->forget(['mfa_user_id', 'mfa_remember']);
        $request->session()->regenerate();

        AuditLog::record('login', 'auth', description: 'User logged in with MFA', request: $request);

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request)
    {
        $userId = session('mfa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);

        // Rate limit resend — max once per minute
        if ($user->mfa_code_expires_at && now()->diffInSeconds($user->mfa_code_expires_at) > 240) {
            return back()->withErrors(['code' => 'Please wait before requesting a new code.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'mfa_code'            => $code,
            'mfa_code_expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new MfaOtpMail($code, $user->name));

        return back()->with('success', 'A new code has been sent to your email.');
    }

    public function toggle(Request $request)
    {
        $user = $request->user();
        
        // Explicitly cast to bool to handle null values from DB
        $currentState = (bool) $user->mfa_enabled;
        
        $user->update(['mfa_enabled' => !$currentState]);

        $status = !$currentState ? 'enabled' : 'disabled';

        AuditLog::record('updated', 'auth', description: "MFA {$status} by user");

        return back()->with('success', "Two-factor authentication {$status} successfully.");
    }
}