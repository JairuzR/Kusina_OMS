@extends('layouts.app')

@section('title', 'Inventory Item - KusinaOMS')
@section('page-title', 'Inventory Item')

@section('content')
<div class="mt-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Item Details --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ $inventoryItem->name }}</h2>
                    @if($inventoryItem->quantity == 0)
                        <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-600">Out of Stock</span>
                    @elseif($inventoryItem->isLowStock())
                        <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">Low Stock</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">In Stock</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Current Stock</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($inventoryItem->quantity, 2) }} {{ $inventoryItem->unit }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Minimum Stock</p>
                        <p class="text-2xl font-bold text-gray-400">{{ number_format($inventoryItem->min_quantity, 2) }} {{ $inventoryItem->unit }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">SKU</p>
                        <p class="font-medium text-gray-800">{{ $inventoryItem->sku ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Category</p>
                        <p class="font-medium text-gray-800">{{ $inventoryItem->category ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Cost Per Unit</p>
                        <p class="font-medium text-gray-800">{{ $inventoryItem->cost_per_unit ? '₱' . number_format($inventoryItem->cost_per_unit, 2) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Supplier</p>
                        <p class="font-medium text-gray-800">{{ $inventoryItem->supplier?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Storage Location</p>
                        <p class="font-medium text-gray-800">{{ $inventoryItem->storage_location ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Expiry Date</p>
                        <p class="font-medium text-gray-800">{{ $inventoryItem->expiry_date?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Transaction History</h3>
                @forelse($transactions as $tx)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0 text-sm">
                    <div>
                        <span class="px-2 py-0.5 rounded text-xs
                            @if($tx->type === 'in') bg-green-100 text-green-700
                            @elseif($tx->type === 'out') bg-red-100 text-red-600
                            @elseif($tx->type === 'waste') bg-orange-100 text-orange-600
                            @else bg-blue-100 text-blue-600 @endif">
                            {{ ucfirst($tx->type) }}
                        </span>
                        <span class="ml-2 text-gray-600">{{ $tx->reason }}</span>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-800">{{ number_format($tx->quantity, 2) }} {{ $inventoryItem->unit }}</p>
                        <p class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No transactions yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Actions --}}
        <div class="space-y-4">
            @can('edit inventory')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Adjust Stock</h3>
                <form action="{{ route('inventory.adjust', $inventoryItem) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="waste">Waste</option>
                            <option value="adjustment">Manual Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" min="0.01" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <input type="text" name="reason"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                               placeholder="e.g. New delivery, Used in kitchen">
                    </div>
                    <button type="submit"
                            class="w-full bg-orange-500 text-white py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                        Adjust Stock
                    </button>
                </form>
            </div>
            @endcan

            <a href="{{ route('inventory.edit', $inventoryItem) }}"
               class="block text-center bg-gray-700 text-white py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                Edit Item
            </a>
            <a href="{{ route('inventory.index') }}"
               class="block text-center bg-gray-100 text-gray-700 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                Back to Inventory
            </a>
        </div>

    </div>
</div>
@endsection