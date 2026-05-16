<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KusinaOMS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /*
         * Fallback for sidebar nav text — Tailwind JIT may purge dynamic
         * ternary classes in Blade. These explicit rules guarantee visibility
         * regardless of the purge result.
         */
        #sidebar nav a,
        #sidebar nav button {
            color: #d1d5db; /* gray-300 */
        }
        #sidebar nav a:hover,
        #sidebar nav button:hover {
            background-color: #374151; /* gray-700 */
            color: #ffffff;
        }
        #sidebar nav a.active-nav {
            background-color: #f97316; /* orange-500 */
            color: #ffffff;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

{{-- <div class="flex min-h-screen"></div> --}}

    {{-- Sidebar --}}
    <aside id="sidebar" class="w-64 bg-gray-900 text-white flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-200">

        {{-- Logo --}}
        <div class="flex items-center justify-between h-16 px-6 bg-gray-800 border-b border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="text-orange-400 font-bold text-xl">Kusina</span>
                <span class="text-white font-bold text-xl">OMS</span>
            </a>
            <button id="closeSidebar" class="md:hidden text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- User Info --}}
        <div class="px-6 py-4 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-9 h-9 rounded-full object-cover">
                <div class="overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->getRoleNames()->first() }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            @can('view dashboard')
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            @endcan

            @can('view orders')
            <a href="{{ route('orders.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('orders.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Orders
            </a>
            @endcan

            @can('view kitchen')
            <a href="{{ route('kitchen.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('kitchen.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                </svg>
                Kitchen Display
            </a>
            @endcan

            @can('view tables')
            <a href="{{ route('tables.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('tables.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Tables
            </a>
            @endcan

            @can('view reservations')
            <a href="{{ route('reservations.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reservations.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Reservations
            </a>
            @endcan

            @can('view menu')
            <a href="{{ route('menu.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('menu.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Menu
            </a>
            @endcan

            @can('view inventory')
            <a href="{{ route('inventory.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('inventory.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Inventory
            </a>
            @endcan

            @can('view reports')
            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reports.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Reports
            </a>
            @endcan

            {{-- Admin Section --}}
            @canany(['view users', 'view audit logs', 'view settings'])
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider" style="color:#6b7280;">Administration</p>
            </div>
            @endcanany

            @can('view users')
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('users.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>
            @endcan

            @can('view audit logs')
            <a href="{{ route('audit-logs.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('audit-logs.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Audit Logs
            </a>
            @endcan

            @can('view settings')
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('settings.*') ? 'active-nav' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
            @endcan

        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm transition">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- Main Content --}}
    {{-- <div class="flex-1 flex flex-col md:ml-64 min-h-screen"> --}}
    <div class="flex-1 flex flex-col md:ml-64 overflow-y-auto">

        {{-- Impersonation Banner --}}
        @if(session('impersonating'))
        <div class="bg-yellow-400 text-yellow-900 text-sm font-medium text-center py-2 px-4 flex items-center justify-center gap-4">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            You are viewing as <strong class="mx-1">{{ auth()->user()->name }}</strong>
            <form method="POST" action="{{ route('users.stop-impersonating') }}" class="inline">
                @csrf
                <button type="submit" class="underline font-semibold hover:text-yellow-800">Stop Impersonating</button>
            </form>
        </div>
        @endif

        {{-- Topbar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <button id="openSidebar" class="md:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-3">

                {{-- Notification Bell --}}
                <div class="relative" id="notifWrapper">
                    <button id="notifToggle"
                            class="relative p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                        <span id="notifBadge"
                              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold px-1 {{ $unreadCount > 0 ? '' : 'hidden' }}">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    </button>

                    {{-- Dropdown panel --}}
                    <div id="notifDropdown"
                         class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <span class="font-semibold text-gray-900 text-sm">Notifications</span>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:underline">Mark all read</button>
                                </form>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-orange-500 hover:underline">View all</a>
                            </div>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-50" id="notifList">
                            @forelse(auth()->user()->notifications()->latest()->take(6)->get() as $notif)
                            @php $d = $notif->data; $isUnread = is_null($notif->read_at); @endphp
                            <a href="{{ $d['url'] ?? route('notifications.index') }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $isUnread ? 'bg-orange-50' : '' }}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5
                                    {{ ($d['type'] ?? '') === 'warning' ? 'bg-yellow-100' : (($d['type'] ?? '') === 'error' ? 'bg-red-100' : 'bg-blue-100') }}">
                                    @if(($d['type'] ?? '') === 'warning')
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    @elseif(($d['type'] ?? '') === 'error')
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $d['title'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $d['message'] ?? '' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                @if($isUnread)
                                    <span class="flex-shrink-0 w-2 h-2 bg-orange-500 rounded-full mt-2"></span>
                                @endif
                            </a>
                            @empty
                            <div class="px-4 py-8 text-center">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-sm text-gray-400">No notifications yet</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User Menu --}}
                <div class="flex items-center gap-2">
                    <img src="{{ auth()->user()->avatar_url }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover border border-gray-200">
                    <a href="{{ route('profile.edit') }}"
                       class="text-sm font-medium text-gray-700 hover:text-orange-500 transition hidden sm:block">
                        {{ auth()->user()->name }}
                    </a>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-6 pt-4 space-y-2" id="flashMessages">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-sm">{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-yellow-500 hover:text-yellow-700 ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="text-sm list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 px-6 pb-6">
            @yield('content')
        </main>

    </div>

</div>

{{-- Mobile Sidebar Overlay --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<script>
    // Mobile sidebar toggle
    const sidebar    = document.getElementById('sidebar');
    const overlay    = document.getElementById('sidebarOverlay');
    const openBtn    = document.getElementById('openSidebar');
    const closeBtn   = document.getElementById('closeSidebar');

    openBtn?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Notification bell dropdown
    const notifToggle   = document.getElementById('notifToggle');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifBadge    = document.getElementById('notifBadge');

    notifToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', (e) => {
        if (!document.getElementById('notifWrapper')?.contains(e.target)) {
            notifDropdown?.classList.add('hidden');
        }
    });

    // Poll unread count every 60 seconds
    function refreshNotifBadge() {
        fetch('{{ route("notifications.count") }}')
            .then(r => r.json())
            .then(data => {
                if (data.count > 0) {
                    notifBadge.textContent = data.count > 9 ? '9+' : data.count;
                    notifBadge.classList.remove('hidden');
                } else {
                    notifBadge.classList.add('hidden');
                }
            })
            .catch(() => {});
    }
    setInterval(refreshNotifBadge, 60000);

    // Auto-dismiss flash messages after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('#flashMessages > div').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);
</script>

@stack('scripts')

</body>
</html>