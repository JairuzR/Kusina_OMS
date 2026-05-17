<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Mail\MfaOtpMail;
use Illuminate\Support\Facades\Mail;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check MFA
        if ($user->mfa_enabled) {
            // Generate OTP
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'mfa_code'            => $code,
                'mfa_code_expires_at' => now()->addMinutes(5),
            ]);

            // Store in session, log out temporarily
            session([
                'mfa_user_id' => $user->id,
                'mfa_remember' => $request->boolean('remember'),
            ]);
            Auth::logout();

            // Send OTP email
            Mail::to($user->email)->send(new MfaOtpMail($code, $user->name));

            return redirect()->route('mfa.verify');
        }

        // Normal login
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'login',
            'module'      => 'auth',
            'description' => 'User logged in',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'logout',
                'module'      => 'auth',
                'description' => 'User logged out',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}