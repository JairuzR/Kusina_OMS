<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been suspended. Please contact the administrator.']);
            }

            if ($user->status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account is inactive. Please contact the administrator.']);
            }
        }

        return $next($request);
    }
}