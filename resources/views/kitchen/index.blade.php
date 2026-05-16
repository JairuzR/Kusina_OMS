@extends('layouts.app')

@section('title', 'Kitchen Display - KusinaOMS')
@section('page-title', 'Kitchen Display')

@section('content')
<div class="mt-4">

    {{-- Auto-refresh notice --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
            Active orders — auto-refreshes every 30 seconds
        </p>
        <button onclick="location.reload()"
                class="text-sm text-orange-500 hover:text-orange-600 border border-orange-300 px-3 py-1.5 rounded-lg">
            Refresh Now
        </button>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
            <p class="text-2xl font-bold text-gray-300 mb-2">All Clear</p>
            <p class="text-gray-400">No active orders in the kitchen.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($orders as $order)
            @php
                $minutesAgo = $order->created_at->diffInMinutes(now());
                $urgency = $minutesAgo >= 20 ? 'red' : ($minutesAgo >= 10 ? 'yellow' : 'green');
            @endphp
            <div class="bg-white rounded-xl shadow-sm border-2
                @if($urgency === 'red') border-red-400
                @elseif($urgency === 'yellow') border-yellow-400
                @else border-green-300 @endif">

                {{-- Order Header --}}
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between
                    @if($urgency === 'red') bg-red-50
                    @elseif($urgency === 'yellow') bg-yellow-50
                    @else bg-green-50 @endif">
                    <div>
                        <p class="font-bold text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $order->table?->name ?? 'Takeout' }} &bull; {{ $order->guests }} guest(s)
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            @if($urgency === 'red') bg-red-100 text-red-700
                            @elseif($urgency === 'yellow') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ $minutesAgo }}m ago
                        </span>
                        <p class="text-xs text-gray-400 mt-0.5 capitalize">{{ $order->status }}</p>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="p-4 space-y-2">
                    @foreach($order->items as $item)
                    @if($item->status !== 'served')
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-800 text-sm">{{ $item->quantity }}x</span>
                                <span class="text-sm text-gray-700">{{ $item->menuItem->name }}</span>
                            </div>
                            @if($item->special_instructions)
                                <p class="text-xs text-orange-500 ml-5">{{ $item->special_instructions }}</p>
                            @endif
                        </div>
                        <div class="ml-2">
                            <form action="{{ route('kitchen.items.status', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                @if($item->status === 'pending')
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit"
                                            class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                                        Start
                                    </button>
                                @elseif($item->status === 'preparing')
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit"
                                            class="text-xs px-2 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200 transition">
                                        Ready
                                    </button>
                                @elseif($item->status === 'ready')
                                    <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">
                                        Ready
                                    </span>
                                @endif
                            </form>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                {{-- Order Notes --}}
                @if($order->notes)
                <div class="px-4 pb-3">
                    <p class="text-xs text-gray-500 bg-gray-50 rounded p-2">
                        Note: {{ $order->notes }}
                    </p>
                </div>
                @endif

            </div>
            @endforeach
        </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setTimeout(() => location.reload(), 30000);
</script>
@endpush