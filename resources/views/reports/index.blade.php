@extends('layouts.app')
@section('title', 'Reports — KusinaOMS')
@section('page-title', 'Reports')

@section('content')
<div class="mt-4 space-y-6">

    {{-- Period Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Period</label>
                <select name="period" id="periodSelect"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                    @foreach([
                        'today'      => 'Today',
                        'yesterday'  => 'Yesterday',
                        'this_week'  => 'This Week',
                        'last_week'  => 'Last Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year'  => 'This Year',
                        'custom'     => 'Custom Range',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div id="customRange" class="{{ $period === 'custom' ? 'flex' : 'hidden' }} items-end gap-2">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                Apply
            </button>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('reports.export', array_merge(request()->query(), ['type' => 'sales'])) }}"
                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Sales
                </a>
                <a href="{{ route('reports.export', array_merge(request()->query(), ['type' => 'inventory'])) }}"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Inventory
                </a>
                <a href="{{ route('pdf.reports.sales', request()->query()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Sales PDF
                </a>
                <a href="{{ route('pdf.reports.inventory') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Inventory PDF
                </a>
            </div>
        </form>
        <p class="text-xs text-gray-400 mt-2">
            Showing data from <strong>{{ $from->format('M j, Y') }}</strong> to <strong>{{ $to->format('M j, Y') }}</strong>
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Completed Orders</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($totalOrders) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Average Order</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">₱{{ number_format($averageOrder, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Discounts</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">₱{{ number_format($totalDiscount, 2) }}</p>
        </div>
    </div>

    {{-- Revenue Chart + Orders by Type --}}
    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4">Daily Revenue</h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4">Orders by Type</h2>
            <canvas id="typeChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                @foreach($ordersByType as $row)
                <div class="flex items-center justify-between text-sm">
                    <span class="capitalize text-gray-600">{{ str_replace('_', ' ', $row->type) }}</span>
                    <span class="font-medium text-gray-900">{{ $row->count }} orders · ₱{{ number_format($row->revenue, 0) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Items + Low Stock --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- Top Selling Items --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Top Selling Items</h2>
                <a href="{{ route('reports.export', array_merge(request()->query(), ['type' => 'top_items'])) }}"
                   class="text-xs text-orange-500 hover:text-orange-700">Export CSV</a>
            </div>
            @forelse($topItems as $i => $row)
            <div class="flex items-center gap-3 px-5 py-3 {{ $loop->last ? '' : 'border-b border-gray-50' }}">
                <span class="text-sm font-bold text-gray-300 w-5">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $row->menuItem?->name ?? '—' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">{{ number_format($row->total_qty) }} sold</p>
                    <p class="text-xs text-gray-400">₱{{ number_format($row->total_revenue, 0) }}</p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 text-sm py-8">No sales data for this period</p>
            @endforelse
        </div>

        {{-- Low Stock Alert --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Low Stock Items</h2>
                <span class="text-xs text-red-500 font-medium">{{ $lowStockItems->count() }} items</span>
            </div>
            @forelse($lowStockItems as $item)
            <div class="flex items-center gap-3 px-5 py-3 {{ $loop->last ? '' : 'border-b border-gray-50' }}">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->name }}</p>
                    <p class="text-xs text-gray-400">{{ $item->category }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-red-600">{{ $item->quantity }} {{ $item->unit }}</p>
                    <p class="text-xs text-gray-400">Min: {{ $item->min_quantity }}</p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 text-sm py-8">All items are sufficiently stocked</p>
            @endforelse
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Revenue by Payment Method</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Method</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Transactions</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($revenueByPayment as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $row->payment_method) }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $row->count }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-900">₱{{ number_format($row->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-sm">No payment data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Show/hide custom date range
    document.getElementById('periodSelect').addEventListener('change', function () {
        document.getElementById('customRange').classList.toggle('hidden', this.value !== 'custom');
    });

    // Daily Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyRevenue->pluck('date')) !!},
            datasets: [{
                label: 'Revenue (₱)',
                data: {!! json_encode($dailyRevenue->pluck('total')) !!},
                backgroundColor: 'rgba(249, 115, 22, 0.8)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } }
            }
        }
    });

    // Orders by Type Doughnut
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ordersByType->pluck('type')->map(fn($t) => str_replace('_', ' ', ucfirst($t)))) !!},
            datasets: [{
                data: {!! json_encode($ordersByType->pluck('count')) !!},
                backgroundColor: ['#f97316', '#3b82f6', '#10b981'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            cutout: '65%',
        }
    });
</script>
@endpush