@extends('layouts.app')

@section('title', 'Reservations - KusinaOMS')
@section('page-title', 'Reservations')

@section('content')
<div class="mt-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-blue-500">{{ $stats['today'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Today</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-500">{{ $stats['confirmed'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Confirmed</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-orange-500">{{ $stats['upcoming'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Upcoming</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">All reservations</p>
        @can('create reservations')
        <a href="{{ route('reservations.create') }}"
           class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
            New Reservation
        </a>
        @endcan
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Table</th>
                        <th class="px-6 py-3">Party</th>
                        <th class="px-6 py-3">Date & Time</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $reservation->customer_name }}</p>
                            <p class="text-xs text-gray-400">{{ $reservation->customer_phone }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $reservation->table->name }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $reservation->party_size }} pax
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            <p>{{ $reservation->reservation_date->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($reservation->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($reservation->status === 'cancelled') bg-red-100 text-red-600
                                @elseif($reservation->status === 'completed') bg-blue-100 text-blue-700
                                @elseif($reservation->status === 'no_show') bg-gray-100 text-gray-600
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('reservations.show', $reservation) }}"
                                   class="text-blue-600 hover:underline text-xs">View</a>
                                @can('edit reservations')
                                <a href="{{ route('reservations.edit', $reservation) }}"
                                   class="text-gray-600 hover:underline text-xs">Edit</a>
                                @endcan
                                @can('manage order status')
                                @if(!in_array($reservation->status, ['completed', 'cancelled']))
                                <form action="{{ route('reservations.status', $reservation) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="text-xs border border-gray-200 rounded px-1 py-0.5 text-gray-600">
                                        <option value="">Update</option>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="completed">Completed</option>
                                        <option value="no_show">No Show</option>
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            No reservations yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $reservations->links() }}
    </div>

</div>
@endsection