@extends('layouts.app')

@section('title', 'Reservation - KusinaOMS')
@section('page-title', 'Reservation Details')

@section('content')
<div class="mt-4 max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">{{ $reservation->customer_name }}</h2>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($reservation->status === 'confirmed') bg-green-100 text-green-700
                @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-700
                @elseif($reservation->status === 'cancelled') bg-red-100 text-red-600
                @elseif($reservation->status === 'completed') bg-blue-100 text-blue-700
                @else bg-gray-100 text-gray-600 @endif">
                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Phone</p>
                <p class="font-medium text-gray-800">{{ $reservation->customer_phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Email</p>
                <p class="font-medium text-gray-800">{{ $reservation->customer_email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Table</p>
                <p class="font-medium text-gray-800">{{ $reservation->table->name }}</p>
            </div>
            <div>
                <p class="text-gray-500">Party Size</p>
                <p class="font-medium text-gray-800">{{ $reservation->party_size }} pax</p>
            </div>
            <div>
                <p class="text-gray-500">Date</p>
                <p class="font-medium text-gray-800">{{ $reservation->reservation_date->format('F d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Time</p>
                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}</p>
            </div>
            @if($reservation->notes)
            <div class="col-span-2">
                <p class="text-gray-500">Notes</p>
                <p class="font-medium text-gray-800">{{ $reservation->notes }}</p>
            </div>
            @endif
        </div>

        <div class="mt-6 flex gap-3">
            @can('edit reservations')
            <a href="{{ route('reservations.edit', $reservation) }}"
               class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                Edit
            </a>
            @endcan
            @can('delete reservations')
            @if(!in_array($reservation->status, ['completed']))
            <form action="{{ route('reservations.destroy', $reservation) }}" method="POST"
                  onsubmit="return confirm('Delete this reservation?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition">
                    Delete
                </button>
            </form>
            @endif
            @endcan
            <a href="{{ route('reservations.index') }}"
               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                Back
            </a>
        </div>

    </div>
</div>
@endsection