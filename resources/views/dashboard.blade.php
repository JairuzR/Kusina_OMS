@extends('layouts.app')

@section('title', 'Dashboard - KusinaOMS')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 mb-6">

    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Today's Revenue</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">₱{{ number_format($todayRevenue, 2) }}</p>
        <p class="text-xs text-green-500 mt-1">Completed orders today</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Today's Orders</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $todayOrders }}</p>
        <p class="text-xs text-orange-500 mt-1">{{ $pendingOrders }} pending</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Tables</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $occupiedTables }}/{{ $totalTables }}</p>
        <p class="text-xs text-blue-500 mt-1">{{ $availableTables }} available</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Today's Reservations</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $todayReservations }}</p>
        <p class="text-xs text-purple-500 mt-1">Active staff: {{ $totalUsers }}</p>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Weekly Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Weekly Revenue</h3>
        <canvas id="revenueChart" height="120"></canvas>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Recent Orders</h3>
        @forelse($recentOrders as $order)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-700">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-400">{{ $order->table?->name ?? 'Takeout' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">₱{{ number_format($order->total_amount, 2) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        @if($order->status === 'completed') bg-green-100 text-green-700
                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700
                        @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">No orders today yet.</p>
        @endforelse
    </div>

</div>

{{-- Recent Activity --}}
<div class="mt-6 bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <h3 class="font-semibold text-gray-800 mb-4">Recent Activity</h3>
    @forelse($recentAuditLogs as $log)
        <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
            <div class="w-2 h-2 rounded-full
                @if(in_array($log->action, ['created', 'POST'])) bg-green-400
                @elseif(in_array($log->action, ['updated', 'PUT', 'PATCH'])) bg-yellow-400
                @elseif(in_array($log->action, ['deleted', 'DELETE'])) bg-red-400
                @else bg-blue-400 @endif">
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-700">
                    <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                    — {{ $log->description }}
                </p>
                <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center py-4">No recent activity.</p>
    @endforelse
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($weeklyRevenue, 'day')) !!},
            datasets: [{
                label: 'Revenue (₱)',
                data: {!! json_encode(array_column($weeklyRevenue, 'revenue')) !!},
                backgroundColor: 'rgba(249, 115, 22, 0.7)',
                borderColor: 'rgb(249, 115, 22)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₱' + value.toLocaleString()
                    }
                }
            }
        }
    });
</script>
@endpush