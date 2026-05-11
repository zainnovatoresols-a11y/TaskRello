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
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans antialiased">

{{-- ── Top navbar (reuse app navbar) ──────────────────────── --}}
<nav class="bg-blue-700 dark:bg-blue-900 sticky top-0 z-40 shadow-md h-14 flex items-center px-4">
    <div class="flex items-center justify-between w-full">

        {{-- Back to boards --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('boards.index') }}"
               class="text-white/80 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <a href="{{ route('boards.index') }}"
               class="text-white font-bold text-lg tracking-tight hover:opacity-90">
                {{ config('app.name') }}
            </a>
            <span class="text-white/40">|</span>
            <span class="text-white/80 text-sm font-medium">Messages</span>
        </div>

        {{-- User info --}}
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-white/25 flex items-center
                        justify-center text-white font-bold text-xs">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <span class="text-white text-sm hidden sm:block">
                {{ auth()->user()->name }}
            </span>
        </div>
    </div>
</nav>

{{-- ── Main chat layout (3 panels) ────────────────────────── --}}
<div class="flex h-[calc(100vh-56px)]">

    @yield('chat-content')

</div>

@yield('scripts')

</body>
</html>