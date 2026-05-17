@extends('layouts.app')

@section('title', 'Order ' . $order->order_number . ' - KusinaOMS')
@section('page-title', 'Order Details')

@section('content')
<div class="mt-4">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Order Header --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500">
                            {{ $order->table?->name ?? 'Takeout' }} &bull;
                            {{ $order->guests }} guest(s) &bull;
                            {{ ucfirst($order->type) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'completed') bg-green-100 text-green-700
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status === 'preparing') bg-blue-100 text-blue-700
                            @elseif($order->status === 'ready') bg-purple-100 text-purple-700
                            @elseif($order->status === 'billed') bg-orange-100 text-orange-700
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-600
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        @if($order->payment)
                        <a href="{{ route('pdf.order.receipt', $order) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Receipt
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Items --}}
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="pb-2">Item</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-right">Price</th>
                            <th class="pb-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-2">
                                <p class="font-medium text-gray-800">{{ $item->menuItem->name }}</p>
                                @if($item->special_instructions)
                                    <p class="text-xs text-gray-400">{{ $item->special_instructions }}</p>
                                @endif
                            </td>
                            <td class="py-2 text-center text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-2 text-right text-gray-600">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 text-right font-medium text-gray-800">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tax (12%)</span>
                        <span>₱{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Discount ({{ $order->discount_type }})</span>
                        <span>-₱{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-100">
                        <span>Total</span>
                        <span>₱{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            @if($order->payment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Payment</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Method</p>
                        <p class="font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $order->payment->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Amount Paid</p>
                        <p class="font-medium text-gray-800">₱{{ number_format($order->payment->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Change</p>
                        <p class="font-medium text-gray-800">₱{{ number_format($order->payment->change_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Cashier</p>
                        <p class="font-medium text-gray-800">{{ $order->cashier?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Right: Actions --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Order Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Info</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Waiter</span>
                        <span class="text-gray-800">{{ $order->waiter?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-800">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($order->notes)
                    <div>
                        <p class="text-gray-500">Notes</p>
                        <p class="text-gray-800 mt-1">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Update Status --}}
            @can('manage order status')
            @if(!in_array($order->status, ['completed', 'cancelled']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Update Status</h3>
                <form action="{{ route('orders.status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="served" {{ $order->status === 'served' ? 'selected' : '' }}>Served</option>
                        <option value="billed" {{ $order->status === 'billed' ? 'selected' : '' }}>Billed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit"
                            class="w-full bg-gray-700 text-white py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                        Update Status
                    </button>
                </form>
            </div>
            @endif
            @endcan

            {{-- Generate Bill --}}
            @can('process payments')
            @if(in_array($order->status, ['served', 'billed']) && !$order->payment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Generate Bill</h3>
                <form action="{{ route('orders.bill', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                        <select name="discount_type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">No discount</option>
                            <option value="senior">Senior Citizen (20%)</option>
                            <option value="pwd">PWD (20%)</option>
                            <option value="promo">Promo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount (₱)</label>
                        <input type="number" name="discount_amount" value="0" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit"
                            class="w-full bg-orange-500 text-white py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                        Generate Bill
                    </button>
                </form>
            </div>
            @endif
            @endcan

            {{-- Process Payment --}}
            @can('process payments')
            @if($order->status === 'billed' && !$order->payment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Process Payment</h3>
                <form action="{{ route('orders.payment', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (₱)</label>
                        <input type="number" name="amount_paid" min="0" step="0.01"
                               value="{{ $order->total_amount }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference # (optional)</label>
                        <input type="text" name="reference_number"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                               placeholder="For GCash/Card payments">
                    </div>
                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700 transition">
                        Complete Payment
                    </button>
                </form>
            </div>
            @endif
            @endcan

            {{-- Back --}}
            <a href="{{ route('orders.index') }}"
               class="block text-center text-sm text-gray-500 hover:text-gray-700 bg-white rounded-xl shadow-sm border border-gray-100 p-3">
                Back to Orders
            </a>

        </div>
    </div>
</div>
@endsection