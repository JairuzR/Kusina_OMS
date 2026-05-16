@extends('layouts.app')

@section('title', 'Audit Log Detail — KusinaOMS')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('audit-logs.index') }}"
           class="text-gray-500 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Log Entry #{{ $auditLog->id }}</h1>
            <p class="text-sm text-gray-500">{{ $auditLog->created_at->format('F j, Y g:i:s A') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Event Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 border-b pb-2">Event Details</h2>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Action</p>
                    <p class="font-medium text-gray-900 capitalize">{{ $auditLog->action }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Module</p>
                    <p class="font-medium text-gray-900 capitalize">{{ $auditLog->module }}</p>
                </div>
                <div>
                    <p class="text-gray-500">IP Address</p>
                    <p class="font-mono text-gray-900">{{ $auditLog->ip_address ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Model</p>
                    <p class="font-medium text-gray-900">
                        {{ $auditLog->model_type ? class_basename($auditLog->model_type) . ' #' . $auditLog->model_id : '—' }}
                    </p>
                </div>
            </div>

            <div>
                <p class="text-gray-500 text-sm">Description</p>
                <p class="font-medium text-gray-900 mt-1">{{ $auditLog->description ?? '—' }}</p>
            </div>

            <div>
                <p class="text-gray-500 text-sm">User Agent</p>
                <p class="text-gray-700 text-xs mt-1 break-all">{{ $auditLog->user_agent ?? '—' }}</p>
            </div>
        </div>

        {{-- User Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 border-b pb-2">Performed By</h2>
            @if($auditLog->user)
                <div class="flex items-center gap-3">
                    <img src="{{ $auditLog->user->avatar_url }}"
                         class="w-12 h-12 rounded-full object-cover" alt="">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $auditLog->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $auditLog->user->email }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ $auditLog->user->getRoleNames()->first() }}</p>
                    </div>
                </div>
                <a href="{{ route('users.show', $auditLog->user) }}"
                   class="inline-flex items-center gap-1 text-sm text-orange-500 hover:text-orange-700">
                    View user profile
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <p class="text-gray-400 italic">System action (no user)</p>
            @endif
        </div>
    </div>

    {{-- Old vs New Values --}}
    @if($auditLog->old_values || $auditLog->new_values)
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 border-b pb-2 mb-4">
                <span class="text-red-600">Before</span> (Old Values)
            </h2>
            @if($auditLog->old_values)
                <pre class="text-xs text-gray-700 bg-gray-50 rounded-lg p-3 overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            @else
                <p class="text-gray-400 italic text-sm">No previous data (new record)</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 border-b pb-2 mb-4">
                <span class="text-green-600">After</span> (New Values)
            </h2>
            @if($auditLog->new_values)
                <pre class="text-xs text-gray-700 bg-gray-50 rounded-lg p-3 overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            @else
                <p class="text-gray-400 italic text-sm">No new data (deleted record)</p>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection