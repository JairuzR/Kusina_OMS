@extends('layouts.app')

@section('title', 'Notifications — KusinaOMS')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ auth()->user()->unreadNotifications()->count() }} unread
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    Mark All Read
                </button>
            </form>
            @endif
            @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.destroy-all') }}"
                  onsubmit="return confirm('Clear all notifications?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                    Clear All
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 text-center py-16">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500 font-medium">No notifications yet</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = is_null($notification->read_at);
            @endphp
            <div class="bg-white rounded-xl border {{ $isUnread ? 'border-orange-300 shadow-sm' : 'border-gray-200' }} p-4 flex items-start gap-4">
                {{-- Icon --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                    {{ ($data['type'] ?? '') === 'warning' ? 'bg-yellow-100' :
                       (($data['type'] ?? '') === 'error' ? 'bg-red-100' : 'bg-blue-100') }}">
                    @if(($data['type'] ?? '') === 'warning')
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    @elseif(($data['type'] ?? '') === 'error')
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900 {{ $isUnread ? 'font-semibold' : '' }}">
                                {{ $data['title'] ?? 'Notification' }}
                                @if($isUnread)
                                    <span class="ml-2 inline-block w-2 h-2 bg-orange-500 rounded-full"></span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $data['message'] ?? '' }}</p>
                            @if(isset($data['url']))
                                <a href="{{ $data['url'] }}"
                                   class="text-xs text-orange-500 hover:text-orange-700 mt-1 inline-block">
                                    View details →
                                </a>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($isUnread)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" title="Mark as read"
                                class="text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete"
                                class="text-gray-400 hover:text-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection