<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/echo.js'])
</head>

<body class="h-screen flex flex-col bg-gray-100 dark:bg-gray-900 font-sans antialiased overflow-hidden">
   {{-- ── Top navbar (compact height) ──────────────────────── --}}
<nav class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 
            dark:from-blue-800 dark:via-blue-900 dark:to-indigo-900 
            sticky top-0 z-40 shadow-xl backdrop-blur-xl 
            border-b border-white/10">

    <div class="flex items-center justify-between w-full 
                px-3 sm:px-4 py-1.5 sm:py-2">
            {{-- Back to boards --}}
            <div class="flex items-center gap-1.5 sm:gap-2.5">

                {{-- Back Button --}}
                <a href="{{ route('boards.index') }}"
                    class="text-white/80 hover:text-white transition-all duration-200
              hover:scale-105 p-1.5 rounded-lg hover:bg-white/10">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>

                {{-- App Name --}}
                <a href="{{ route('boards.index') }}"
                    class="text-white font-semibold text-sm sm:text-base tracking-tight
              hover:opacity-90 transition-opacity">
                    {{ config('app.name') }}
                </a>

                {{-- Divider --}}
                <span class="text-white/40 text-xs">|</span>

                {{-- Messages Badge --}}
                <span class="text-white/90 text-[10px] sm:text-xs font-medium
                 bg-white/10 px-2 py-0.5 rounded-full backdrop-blur-sm">
                    Messages
                </span>
            </div>

            {{-- Mobile menu button --}}
            <button onclick="toggleMobileSidebar()"
                class="lg:hidden text-white/80 hover:text-white transition-all duration-200 hover:scale-110 p-2 rounded-xl hover:bg-white/10 mr-2"
                id="mobile-menu-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- User info --}}
            <div class="flex items-center gap-1.5 sm:gap-2">

                {{-- Small Avatar Circle --}}
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full
                bg-white/20 backdrop-blur-sm
                ring-1 ring-white/30
                flex items-center justify-center
                text-white font-semibold text-[10px] sm:text-xs shadow">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>

                {{-- Small Username --}}
                <span class="text-white text-[11px] sm:text-xs font-medium truncate max-w-[90px] sm:max-w-[120px]">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </div>
    </nav>

    {{-- ── Main chat layout (3 panels) ────────────────────────── --}}
<div class="flex h-[calc(100vh-48px)] bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 relative">
        {{-- Mobile overlay backdrop --}}
        <div id="mobile-sidebar-backdrop"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"
            onclick="closeMobileSidebar()"></div>

        @yield('chat-content')

    </div>

    @yield('scripts')

</body>

</html>