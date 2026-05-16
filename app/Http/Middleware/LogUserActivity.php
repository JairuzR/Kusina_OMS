<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (Auth::check() && $request->isMethod('post', 'put', 'patch', 'delete')) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $request->method(),
                'module'     => $this->getModule($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => $request->method() . ' ' . $request->path(),
            ]);
        }
    }

    private function getModule(Request $request): string
    {
        $segments = $request->segments();
        return $segments[0] ?? 'general';
    }
}