<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('module', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs    = $query->paginate(25)->withQueryString();
        $modules = AuditLog::distinct()->pluck('module')->sort()->values();
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();
        $users   = User::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total'   => AuditLog::count(),
            'today'   => AuditLog::whereDate('created_at', today())->count(),
            'logins'  => AuditLog::where('action', 'login')->whereDate('created_at', today())->count(),
            'errors'  => AuditLog::where('action', 'error')->count(),
        ];

        return view('audit-logs.index', compact('logs', 'modules', 'actions', 'users', 'stats'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit-logs.show', compact('auditLog'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'User', 'Action', 'Module', 'Description', 'IP Address', 'User Agent', 'Created At']);

            $query->chunk(500, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->user?->name ?? 'System',
                        $log->action,
                        $log->module,
                        $log->description,
                        $log->ip_address,
                        $log->user_agent,
                        $log->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function destroy(AuditLog $auditLog)
    {
        $auditLog->delete();
        return back()->with('success', 'Log entry deleted.');
    }

    public function purgeOld()
    {
        $count = AuditLog::where('created_at', '<', now()->subDays(90))->delete();
        return back()->with('success', "Purged {$count} log entries older than 90 days.");
    }
}