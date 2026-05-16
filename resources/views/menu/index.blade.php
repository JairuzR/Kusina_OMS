@extends('layouts.app')

@section('title', 'Menu Management - KusinaOMS')
@section('page-title', 'Menu Management')

@section('content')
<div class="mt-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">Manage your menu categories and items</p>
        </div>
        @can('create menu')
        <div class="flex gap-2">
            <a href="{{ route('menu.categories.create') }}"
               class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                Add Category
            </a>
            <a href="{{ route('menu.items.create') }}"
               class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                Add Menu Item
            </a>
        </div>
        @endcan
    </div>

    {{-- Categories with their items --}}
    @forelse($categories as $category)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">

        {{-- Category Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}"
                        class="w-10 h-10 rounded-lg object-cover ring-2 ring-orange-200">
                @else
                    <div class="w-10 h-10 rounded-lg bg-orange-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-sm">{{ substr($category->name, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-gray-900 text-base">{{ $category->name }}</h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $category->items_count }} item(s)</span>
                    </div>
                    <p class="text-xs text-orange-500 font-medium uppercase tracking-wide mt-0.5">Category</p>
                </div>
            </div>
            @can('edit menu')
            <div class="flex items-center gap-3">
                <a href="{{ route('menu.categories.edit', $category) }}"
                class="text-xs text-blue-600 hover:underline">Edit</a>
                <form action="{{ route('menu.categories.destroy', $category) }}" method="POST"
                    onsubmit="return confirm('Delete this category and all its items?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                </form>
            </div>
            @endcan
        </div>

        {{-- Items Table --}}
        @if($category->items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Item</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Prep Time</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Featured</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($category->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                         class="w-8 h-8 rounded object-cover">
                                @else
                                    <div class="w-8 h-8 rounded bg-gray-100"></div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ $item->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-semibold text-gray-800">
                            ₱{{ number_format($item->price, 2) }}
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $item->preparation_time }} min
                        </td>
                        <td class="px-6 py-3">
                            <form action="{{ route('menu.items.toggle', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-2 py-0.5 rounded-full text-xs
                                    {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-3">
                            @if($item->is_featured)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Featured</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @can('edit menu')
                            <div class="flex items-center gap-3">
                                <a href="{{ route('menu.items.edit', $item) }}"
                                   class="text-blue-600 hover:underline text-xs">Edit</a>
                                <form action="{{ route('menu.items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs">Delete</button>
                                </form>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-4 text-sm text-gray-400">
            No items in this category yet.
            @can('create menu')
            <a href="{{ route('menu.items.create') }}" class="text-orange-500 hover:underline ml-1">Add one</a>
            @endcan
        </div>
        @endif

    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <p class="text-gray-400 mb-3">No menu categories yet.</p>
        @can('create menu')
        <a href="{{ route('menu.categories.create') }}"
           class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
            Create First Category
        </a>
        @endcan
    </div>
    @endforelse

    {{ $categories->links() }}

</div>
@endsection