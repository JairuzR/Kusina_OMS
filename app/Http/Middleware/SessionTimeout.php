<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $timeout = config('session.lifetime') * 60; // convert to seconds

            if (session()->has('last_activity')) {
                $lastActivity = session('last_activity');

                if (time() - $lastActivity > $timeout) {
                    Auth::logout();
                    session()->flush();
                    return redirect()->route('login')
                        ->withErrors(['email' => 'Your session has expired. Please log in again.']);
                }
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}