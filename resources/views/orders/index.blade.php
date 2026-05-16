@extends('layouts.app')

@section('title', 'Orders - KusinaOMS')
@section('page-title', 'Orders')

@section('content')
<div class="mt-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-blue-500">{{ $stats['preparing'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Preparing</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-purple-500">{{ $stats['ready'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Ready</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-orange-500">{{ $stats['billed'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Billed</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">All orders</p>
        @can('create orders')
        <a href="{{ route('orders.create') }}"
           class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
            New Order
        </a>
        @endcan
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Order #</th>
                        <th class="px-6 py-3">Table</th>
                        <th class="px-6 py-3">Waiter</th>
                        <th class="px-6 py-3">Items</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Time</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $order->table?->name ?? 'Takeout' }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $order->waiter?->name ?? '—' }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $order->items->count() }} item(s)
                        </td>
                        <td class="px-6 py-3 font-semibold text-gray-800">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($order->status === 'completed') bg-green-100 text-green-700
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                                @elseif($order->status === 'ready') bg-purple-100 text-purple-700
                                @elseif($order->status === 'billed') bg-orange-100 text-orange-700
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-600
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">
                            {{ $order->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="text-blue-600 hover:underline text-xs">View</a>
                                @can('manage order status')
                                @if(!in_array($order->status, ['completed', 'cancelled']))
                                <form action="{{ route('orders.status', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="text-xs border border-gray-200 rounded px-1 py-0.5 text-gray-600">
                                        <option value="">Update</option>
                                        <option value="pending">Pending</option>
                                        <option value="preparing">Preparing</option>
                                        <option value="ready">Ready</option>
                                        <option value="served">Served</option>
                                        <option value="billed">Billed</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            No orders yet.
                            @can('create orders')
                            <a href="{{ route('orders.create') }}" class="text-orange-500 hover:underline ml-1">Create one</a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

</div>
@endsection