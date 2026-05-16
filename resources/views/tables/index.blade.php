@extends('layouts.app')

@section('title', 'Tables - KusinaOMS')
@section('page-title', 'Table Management')

@section('content')
<div class="mt-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Tables</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['available'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Available</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $stats['occupied'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Occupied</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $stats['reserved'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Reserved</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Visual floor map of all active tables</p>
        @can('create tables')
        <a href="{{ route('tables.create') }}"
           class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
            Add Table
        </a>
        @endcan
    </div>

    {{-- Table Grid (Floor Map) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($tables as $table)
        <div class="bg-white rounded-xl shadow-sm border-2 p-4 relative
            @if($table->status === 'available') border-green-300
            @elseif($table->status === 'occupied') border-red-300
            @elseif($table->status === 'reserved') border-yellow-300
            @else border-gray-300 @endif">

            {{-- Status dot --}}
            <div class="absolute top-3 right-3 w-3 h-3 rounded-full
                @if($table->status === 'available') bg-green-400
                @elseif($table->status === 'occupied') bg-red-400
                @elseif($table->status === 'reserved') bg-yellow-400
                @else bg-gray-400 @endif">
            </div>

            {{-- Table icon --}}
            <div class="flex justify-center mb-3">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center
                    @if($table->status === 'available') bg-green-50
                    @elseif($table->status === 'occupied') bg-red-50
                    @elseif($table->status === 'reserved') bg-yellow-50
                    @else bg-gray-50 @endif">
                    <svg class="w-6 h-6
                        @if($table->status === 'available') text-green-500
                        @elseif($table->status === 'occupied') text-red-500
                        @elseif($table->status === 'reserved') text-yellow-500
                        @else text-gray-400 @endif"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
            </div>

            <p class="font-semibold text-gray-800 text-center text-sm">{{ $table->name }}</p>
            <p class="text-xs text-gray-400 text-center">{{ $table->capacity }} seats</p>
            @if($table->location)
                <p class="text-xs text-gray-400 text-center capitalize">{{ $table->location }}</p>
            @endif
            @if($table->waiter)
                <p class="text-xs text-blue-500 text-center mt-1 truncate">{{ $table->waiter->name }}</p>
            @endif

            {{-- Status badge --}}
            <div class="mt-2 text-center">
                <span class="text-xs px-2 py-0.5 rounded-full capitalize
                    @if($table->status === 'available') bg-green-100 text-green-700
                    @elseif($table->status === 'occupied') bg-red-100 text-red-600
                    @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-700
                    @else bg-gray-100 text-gray-500 @endif">
                    {{ str_replace('_', ' ', $table->status) }}
                </span>
            </div>

            {{-- Actions --}}
            @can('edit tables')
            <div class="mt-3 border-t border-gray-100 pt-2 flex items-center justify-between">
                <a href="{{ route('tables.edit', $table) }}"
                class="text-xs text-blue-600 hover:underline px-1">Edit</a>

                <form action="{{ route('tables.status', $table) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded px-1 py-0.5 bg-white text-orange-500 cursor-pointer focus:outline-none">
                        <option value="">Set Status</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="needs_cleaning">Needs Cleaning</option>
                    </select>
                </form>
            </div>
            @endcan

        </div>
        @empty
        <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-400 mb-3">No tables yet.</p>
            @can('create tables')
            <a href="{{ route('tables.create') }}"
               class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                Add First Table
            </a>
            @endcan
        </div>
        @endforelse
    </div>

</div>
@endsection