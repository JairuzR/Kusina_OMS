@extends('layouts.app')
@section('title', 'Audit Logs — KusinaOMS')
@section('content')
<div class="py-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Track all system actions and user activity</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('audit-logs.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
            @if(auth()->user()->hasRole('admin'))
            <form method="POST" action="{{ route('audit-logs.purge') }}"
                  onsubmit="return confirm('Purge all logs older than 90 days?')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Purge Old Logs
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Logs</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Today's Activity</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['today']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Logins Today</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['logins']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Error Events</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['errors']) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('audit-logs.index') }}">
            {{-- Row 1: Search + Module + Action --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search description or module..."
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">

                <select name="module" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ ucfirst($module) }}</option>
                    @endforeach
                </select>

                <select name="action" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Row 2: Date From + Date To + User + Buttons --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">User</label>
                    <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search','module','action','date_from','date_to','user_id']))
                    <a href="{{ route('audit-logs.index') }}"
                       class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                        Clear
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($logs->isEmpty())
            <div class="text-center py-16">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">No log entries found</p>
                <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Time</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Module</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">IP Address</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                            <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $log->user->avatar_url }}"
                                         class="w-6 h-6 rounded-full object-cover flex-shrink-0" alt="">
                                    <span class="font-medium text-gray-900 text-xs">{{ $log->user->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-xs">System</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($log->action_badge) {
                                    'green'  => 'bg-green-100 text-green-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'red'    => 'bg-red-100 text-red-800',
                                    'blue'   => 'bg-blue-100 text-blue-800',
                                    default  => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }} whitespace-nowrap">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 whitespace-nowrap">
                                {{ ucfirst($log->module) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xs">
                            <span class="block truncate" title="{{ $log->description }}">
                                {{ $log->description ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs whitespace-nowrap">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('audit-logs.show', $log) }}"
                               class="text-orange-500 hover:text-orange-700 font-medium text-xs">
                                Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-sm text-gray-500">
                Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries
            </p>
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection