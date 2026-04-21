<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Trello Clone') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-100 dark:bg-gray-900 font-sans antialiased">


    <nav class="bg-gray-700 dark:bg-gray-800 sticky top-0 z-40 shadow-md">
        <div class="max-w-screen-xl mx-auto px-4 flex items-center justify-between h-14">

            <a href="{{ route('boards.index') }}"
                class="text-white font-bold text-lg tracking-tight hover:opacity-90 transition">
                {{ config('app.name', 'TaskRello') }}
            </a>


            <div class="flex items-center gap-4">

                <a href="{{ route('boards.create') }}"
                    class="hidden sm:inline-flex items-center gap-1 text-white/80 hover:text-white text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New board
                </a>

                <div x-data="{ open: false }" class="relative">

                    <button @click="open = !open; if(open) loadNotifications()"
                        class="relative flex items-center justify-center
                   w-9 h-9 rounded-full text-white/80 hover:text-white
                   hover:bg-white/10 transition">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                     6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                     0 11-6 0v-1m6 0H9" />
                        </svg>

                        <span id="notif-badge"
                            class="{{ auth()->user()->unreadNotificationsCount() > 0 ? '' : 'hidden' }}
                     absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500
                     text-white text-xs font-bold rounded-full
                     flex items-center justify-center leading-none">
                            {{ auth()->user()->unreadNotificationsCount() > 9
                ? '9+'
                : auth()->user()->unreadNotificationsCount() }}
                        </span>
                    </button>

                    <div x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800
                rounded-2xl shadow-2xl border border-gray-100
                dark:border-gray-700 z-50 overflow-hidden">

                        <div class="flex items-center justify-between px-4 py-3
                    border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white">
                                Notifications
                            </h3>
                            <button onclick="markAllRead()"
                                class="text-xs text-blue-600 dark:text-blue-400
                           hover:underline font-medium transition">
                                Mark all as read
                            </button>
                        </div>

                        <div id="notif-list"
                            class="max-h-96 overflow-y-auto divide-y divide-gray-50
                    dark:divide-gray-700/50">
                            <div class="flex items-center justify-center py-10">
                                <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent
                            rounded-full animate-spin"></div>
                            </div>
                        </div>

                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700
                    text-center">
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                Showing last 30 notifications
                            </span>
                        </div>
                    </div>
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 text-white text-sm hover:opacity-80 transition focus:outline-none">
                        <span class="w-8 h-8 rounded-full bg-white/25 flex items-center justify-center font-bold text-xs ring-2 ring-white/30">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                        <span class="hidden sm:block max-w-[120px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">

                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile settings
                        </a>

                        <a href="{{ route('boards.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            My boards
                        </a>

                        <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
    <div class="max-w-screen-xl mx-auto px-4 mt-4" id="flash-success">
        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 rounded-xl px-4 py-3 text-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            <button onclick="document.getElementById('flash-success').remove()"
                class="text-green-600 dark:text-green-400 hover:text-green-800 font-bold ml-4 text-lg leading-none">
                &times;
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-screen-xl mx-auto px-4 mt-4" id="flash-error">
        <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
            <button onclick="document.getElementById('flash-error').remove()"
                class="text-red-600 dark:text-red-400 hover:text-red-800 font-bold ml-4 text-lg leading-none">
                &times;
            </button>
        </div>
    </div>
    @endif

    <main>
        @yield('content')
    </main>
    <script src="{{ asset('js/notifications.js') }}"></script>
    @yield('scripts')

</body>

</html>