@extends('layouts.app')

@section('title', 'Users - KusinaOMS')
@section('page-title', 'User Management')

@section('content')
<div class="mt-4">

    {{-- Impersonation Banner --}}
    @if(session('impersonating'))
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
        <span class="text-sm">You are currently viewing as <strong>{{ auth()->user()->name }}</strong></span>
        <form action="{{ route('users.stop-impersonating') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">
                Return to My Account
            </button>
        </form>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Users</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-500">{{ $stats['active'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Active</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-400">{{ $stats['inactive'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Inactive</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $stats['suspended'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Suspended</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name or email..."
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Role</label>
                <select name="role"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <button type="submit"
                    class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                Filter
            </button>
            <a href="{{ route('users.index') }}"
               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                Reset
            </a>
            <div class="ml-auto">
                <a href="{{ route('users.create') }}"
                   class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                    Add User
                </a>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Last Login</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}"
                                     class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700 capitalize">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                @if($user->status === 'active') bg-green-100 text-green-700
                                @elseif($user->status === 'suspended') bg-red-100 text-red-600
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">
                            {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-blue-600 hover:underline text-xs">View</a>
                                @if($user->id !== auth()->id())
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-gray-600 hover:underline text-xs">Edit</a>
                                <form action="{{ route('users.impersonate', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="text-orange-500 hover:underline text-xs">
                                        Login As
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

</div>
@endsection