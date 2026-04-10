<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="h-full bg-gray-100 dark:bg-gray-900 font-sans antialiased">

    <div class="min-h-full flex flex-col items-center justify-center px-4 py-16">

        {{-- Error code --}}
        <div class="text-8xl font-black text-blue-700 dark:text-blue-500 mb-2
                    leading-none tracking-tight select-none">
            404
        </div>

        {{-- Icon --}}
        <div class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full
                    flex items-center justify-center mb-6 mt-4">
            <svg class="w-10 h-10 text-yellow-500 dark:text-yellow-400"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 text-center">
            Page not found
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm text-center max-w-sm mb-8">
            The board, card, or page you are looking for does not exist
            or may have been deleted. Check the URL and try again.
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

        {{-- Helpful suggestions --}}
        <div class="mt-10 bg-white dark:bg-gray-800 border border-gray-100
                    dark:border-gray-700 rounded-2xl p-6 max-w-sm w-full">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400
                      uppercase tracking-wider mb-3">
                What you can do
            </p>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('boards.index') }}"
                        class="flex items-center gap-2 text-sm text-blue-700
                              dark:text-blue-400 hover:underline">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                        </svg>
                        View all my boards
                    </a>
                </li>
                <li>
                    <a href="{{ route('boards.create') }}"
                        class="flex items-center gap-2 text-sm text-blue-700
                              dark:text-blue-400 hover:underline">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create a new board
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 text-sm text-blue-700
                              dark:text-blue-400 hover:underline">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Go to profile settings
                    </a>
                </li>
            </ul>
        </div>

        {{-- App link --}}
        <a href="{{ route('boards.index') }}"
            class="mt-8 text-blue-700 dark:text-blue-400 font-bold text-lg
                  hover:opacity-80 transition">
            {{ config('app.name') }}
        </a>
    </div>

</body>

</html>