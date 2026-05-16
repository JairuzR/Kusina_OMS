@extends('layouts.app')

@section('title', 'User Profile - KusinaOMS')
@section('page-title', 'User Profile')

@section('content')
<div class="mt-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <img src="{{ $user->avatar_url }}"
                     class="w-24 h-24 rounded-full object-cover mx-auto mb-4">
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700 capitalize">
                    {{ $user->getRoleNames()->first() ?? '—' }}
                </span>
                <div class="mt-2">
                    <span class="px-3 py-1 rounded-full text-xs
                        @if($user->status === 'active') bg-green-100 text-green-700
                        @elseif($user->status === 'suspended') bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-left space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phone</span>
                        <span class="text-gray-800">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last Login</span>
                        <span class="text-gray-800">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last IP</span>
                        <span class="text-gray-800">{{ $user->last_login_ip ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Joined</span>
                        <span class="text-gray-800">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                @if($user->id !== auth()->id())
                <div class="mt-4 space-y-2">
                    <a href="{{ route('users.edit', $user) }}"
                       class="block w-full bg-orange-500 text-white py-2 rounded-lg text-sm hover:bg-orange-600 transition text-center">
                        Edit User
                    </a>
                    <form action="{{ route('users.impersonate', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-gray-700 text-white py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                            Login As This User
                        </button>
                    </form>

                    {{-- Status Change --}}
                    <form action="{{ route('users.status', $user) }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                                class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        <button type="submit"
                                class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-xs hover:bg-gray-200">
                            Update
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Recent Activity</h3>
                @forelse($recentLogs as $log)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-2 h-2 rounded-full
                        @if($log->action === 'created') bg-green-400
                        @elseif($log->action === 'updated') bg-yellow-400
                        @elseif($log->action === 'deleted') bg-red-400
                        @else bg-blue-400 @endif flex-shrink-0">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700">{{ $log->description }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }} &bull; {{ $log->ip_address }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">{{ $log->module }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No activity yet.</p>
                @endforelse
            </div>
        </div>

    </div>

    <div class="mt-4">
        <a href="{{ route('users.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">Back to Users</a>
    </div>
</div>
@endsection