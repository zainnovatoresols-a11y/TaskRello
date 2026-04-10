<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Access Denied | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="h-full bg-gray-100 dark:bg-gray-900 font-sans antialiased">

    <div class="min-h-full flex flex-col items-center justify-center px-4 py-16">

        {{-- Error code --}}
        <div class="text-8xl font-black text-blue-700 dark:text-blue-500 mb-2
                    leading-none tracking-tight select-none">
            403
        </div>

        {{-- Icon --}}
        <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full
                    flex items-center justify-center mb-6 mt-4">
            <svg class="w-10 h-10 text-red-500 dark:text-red-400"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-6V7m0 0a4 4 0 100-8 4 4 0 000 8z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 7a9 9 0 0118 0v4a9 9 0 01-18 0V7z" />
            </svg>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 text-center">
            Access denied
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm text-center max-w-sm mb-8">
            You do not have permission to access this page.
            You may not be a member of this board, or you may not
            have the required role to perform this action.
        </p>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('boards.index') }}"
                class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700
                      hover:bg-gray-300 dark:hover:bg-gray-600
                      text-gray-700 dark:text-gray-200
                      px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go back
            </a>
            <a href="{{ route('boards.index') }}"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800
                      text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                My boards
            </a>
        </div>

        {{-- App link --}}
        <a href="{{ route('boards.index') }}"
            class="mt-10 text-blue-700 dark:text-blue-400 font-bold text-lg
                  hover:opacity-80 transition">
            {{ config('app.name') }}
        </a>
    </div>

</body>

</html>