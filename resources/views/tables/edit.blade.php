@extends('layouts.app')

@section('title', 'Edit Table - KusinaOMS')
@section('page-title', 'Edit Table')

@section('content')
<div class="mt-4 max-w-lg">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form action="{{ route('tables.update', $table) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $table->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="50"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select name="location"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Select location</option>
                    <option value="indoor" {{ old('location', $table->location) === 'indoor' ? 'selected' : '' }}>Indoor</option>
                    <option value="outdoor" {{ old('location', $table->location) === 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                    <option value="vip" {{ old('location', $table->location) === 'vip' ? 'selected' : '' }}>VIP</option>
                    <option value="bar" {{ old('location', $table->location) === 'bar' ? 'selected' : '' }}>Bar</option>
                    <option value="private" {{ old('location', $table->location) === 'private' ? 'selected' : '' }}>Private Room</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Waiter</label>
                <select name="assigned_waiter_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">No assigned waiter</option>
                    @foreach($waiters as $waiter)
                        <option value="{{ $waiter->id }}"
                            {{ old('assigned_waiter_id', $table->assigned_waiter_id) == $waiter->id ? 'selected' : '' }}>
                            {{ $waiter->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $table->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-orange-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-orange-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                    Update Table
                </button>
                <a href="{{ route('tables.index') }}"
                   class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</div>
@endsection