@extends('layouts.app')

@section('title', 'AI Inventory Dashboard - KusinaOMS')
@section('page-title', 'AI Inventory Dashboard')

@section('content')
<div class="mt-4">

    {{-- Provider Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @forelse($stats as $stat)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 capitalize mb-3">{{ $stat->provider }}</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-gray-400">Total Calls</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($stat->total_calls) }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Success Rate</p>
                    <p class="text-xl font-bold text-green-600">
                        {{ $stat->total_calls > 0 ? number_format(($stat->successful / $stat->total_calls) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center text-gray-400">
            No AI usage yet.
        </div>
        @endforelse
    </div>

    {{-- Pending Suggestions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <h3 class="font-semibold text-gray-700 mb-4">Pending Reorder Suggestions</h3>
        @forelse($suggestions as $suggestion)
        <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <p class="font-medium text-gray-800">{{ $suggestion->inventoryItem->name }}</p>
                    <span class="px-2 py-0.5 rounded-full text-xs capitalize
                        @if($suggestion->urgency === 'critical') bg-red-100 text-red-700
                        @elseif($suggestion->urgency === 'high') bg-orange-100 text-orange-700
                        @elseif($suggestion->urgency === 'medium') bg-yellow-100 text-yellow-700
                        @else bg-blue-100 text-blue-700 @endif">
                        {{ ucfirst($suggestion->urgency) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500">
                    Order {{ number_format($suggestion->suggested_quantity, 2) }} {{ $suggestion->inventoryItem->unit }}
                    @if($suggestion->estimated_days_until_stockout)
                        &bull; Stockout in {{ $suggestion->estimated_days_until_stockout }} days
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 ml-4">
                <a href="{{ route('inventory.show', $suggestion->inventoryItem) }}"
                   class="text-xs text-blue-600 hover:underline">View</a>
                <form action="{{ route('inventory.ai.acted', $suggestion) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                        Done
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">No pending suggestions.</p>
        @endforelse
    </div>

    {{-- Recent Logs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-700 mb-4">Recent Usage Logs</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="pb-2">Provider</th>
                        <th class="pb-2">Feature</th>
                        <th class="pb-2">Model</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentLogs as $log)
                    <tr>
                        <td class="py-2 capitalize">{{ $log->provider }}</td>
                        <td class="py-2">{{ $log->feature }}</td>
                        <td class="py-2 text-gray-500">{{ $log->model_used }}</td>
                        <td class="py-2">
                            @if($log->success)
                                <span class="text-green-600 font-medium text-xs">Success</span>
                            @else
                                <span class="text-red-500 font-medium text-xs" title="{{ $log->error_message }}">Failed</span>
                            @endif
                        </td>
                        <td class="py-2 text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection