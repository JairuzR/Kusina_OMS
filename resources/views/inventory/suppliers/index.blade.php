@extends('layouts.app')

@section('title', 'Suppliers - KusinaOMS')
@section('page-title', 'Suppliers')

@section('content')
<div class="mt-4">
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('inventory.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Back to Inventory
        </a>
        @can('create inventory')
        <a href="{{ route('inventory.suppliers.create') }}"
           class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
            Add Supplier
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Supplier</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Items</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $supplier->name }}</p>
                            <p class="text-xs text-gray-400">{{ $supplier->email }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $supplier->contact_person ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $supplier->phone ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $supplier->inventory_items_count }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                {{ $supplier->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($supplier->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            @can('edit inventory')
                            <div class="flex gap-2">
                                <a href="{{ route('inventory.suppliers.edit', $supplier) }}"
                                   class="text-blue-600 hover:underline text-xs">Edit</a>
                                <form action="{{ route('inventory.suppliers.destroy', $supplier) }}" method="POST"
                                      onsubmit="return confirm('Delete this supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs">Delete</button>
                                </form>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No suppliers yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $suppliers->links() }}</div>
</div>
@endsection