@extends('layouts.app')

@section('title', 'Backup System — KusinaOMS')
@section('page-title', 'Backup System')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Database Backups</h1>
            <p class="text-sm text-gray-500 mt-1">Manual and scheduled backups of your database</p>
        </div>
        <form method="POST" action="{{ route('backups.store') }}"
              onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='Running…'">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Run Manual Backup
            </button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Backups</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Completed</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Failed</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $stats['failed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Size</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">
                @php
                    $bytes = $stats['size'];
                    echo $bytes >= 1048576
                        ? number_format($bytes / 1048576, 1) . ' MB'
                        : number_format($bytes / 1024, 1) . ' KB';
                @endphp
            </p>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-medium">Automatic backups are scheduled based on your
                <a href="{{ route('settings.index') }}" class="underline hover:text-blue-600">Backup Settings</a>.
            </p>
            <p class="text-blue-600 mt-0.5">
                Backups are stored in <code class="bg-blue-100 px-1 rounded">storage/app/backups/</code>
                and retained for 30 days. Email notifications are sent on each backup event.
            </p>
        </div>
    </div>

    {{-- Backup Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($backups->isEmpty())
            <div class="text-center py-16">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <p class="text-gray-500 font-medium">No backups yet</p>
                <p class="text-gray-400 text-sm mt-1">Click "Run Manual Backup" to create your first backup</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Filename</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Size</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Created By</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                <span class="font-mono text-xs text-gray-700 truncate max-w-xs">{{ $backup->filename }}</span>
                            </div>
                            @if($backup->notes)
                                <p class="text-xs text-red-500 mt-0.5 ml-6 truncate max-w-xs" title="{{ $backup->notes }}">
                                    {{ Str::limit($backup->notes, 60) }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $backup->type === 'manual' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($backup->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $backup->formatted_size }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $color = match($backup->status) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'failed'    => 'bg-red-100 text-red-800',
                                    default     => 'bg-yellow-100 text-yellow-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst($backup->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $backup->creator?->name ?? 'Scheduler' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            <span title="{{ $backup->created_at->format('Y-m-d H:i:s') }}">
                                {{ $backup->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($backup->status === 'completed')
                                    <a href="{{ route('backups.download', $backup) }}"
                                       class="text-xs text-blue-600 hover:text-blue-800 font-medium transition">
                                        Download
                                    </a>
                                    <form method="POST" action="{{ route('backups.verify', $backup) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-xs text-green-600 hover:text-green-800 font-medium transition">
                                            Verify
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('backups.destroy', $backup) }}"
                                      onsubmit="return confirm('Delete this backup permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $backups->firstItem() }}–{{ $backups->lastItem() }} of {{ $backups->total() }} backups
            </p>
            {{ $backups->links() }}
        </div>
        @endif
    </div>

</div>
@endsection