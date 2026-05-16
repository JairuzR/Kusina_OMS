@extends('layouts.app')

@section('title', 'Inventory - KusinaOMS')
@section('page-title', 'Inventory')

@section('content')
<div class="mt-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Items</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $stats['low_stock'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Low Stock</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $stats['out_of_stock'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Out of Stock</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-blue-500">{{ $stats['suppliers'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Suppliers</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">All inventory items</p>
        @can('create inventory')
        <div class="flex gap-2">
            <a href="{{ route('inventory.suppliers.index') }}"
               class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                Suppliers
            </a>
            <a href="{{ route('inventory.create') }}"
               class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                Add Item
            </a>
        </div>
        @endcan
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Item</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Stock</th>
                        <th class="px-6 py-3">Unit</th>
                        <th class="px-6 py-3">Supplier</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $item->name }}</p>
                            @if($item->sku)
                                <p class="text-xs text-gray-400">SKU: {{ $item->sku }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->category ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="font-semibold {{ $item->quantity <= $item->min_quantity ? 'text-red-500' : 'text-gray-800' }}">
                                {{ number_format($item->quantity, 2) }}
                            </span>
                            <span class="text-xs text-gray-400">/ min: {{ number_format($item->min_quantity, 2) }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->unit }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->supplier?->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            @if($item->quantity == 0)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-600">Out of Stock</span>
                            @elseif($item->quantity <= $item->min_quantity)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Low Stock</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">In Stock</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('inventory.show', $item) }}"
                                   class="text-blue-600 hover:underline text-xs">View</a>
                                @can('edit inventory')
                                <a href="{{ route('inventory.edit', $item) }}"
                                   class="text-gray-600 hover:underline text-xs">Edit</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            No inventory items yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

</div>
@endsection