<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Page Loader -->
        <div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <!-- Backdrop with blur -->
            <div class="absolute inset-0 bg-gray-100/80 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            <!-- Loader Container -->
            <div class="relative flex flex-col items-center gap-6 p-8 bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-2xl border border-white/20 dark:border-gray-700/20">
                <!-- Animated Logo/Icon -->
                <div class="relative">
                    <!-- Outer ring -->
                    <div class="w-16 h-16 border-4 border-blue-200 dark:border-blue-800 rounded-full animate-spin"></div>
                    <!-- Inner ring -->
                    <div class="absolute inset-2 border-4 border-transparent border-t-blue-500 rounded-full animate-spin" style="animation-duration: 0.8s; animation-direction: reverse;"></div>
                    <!-- Center dot -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Loading Text with Animation -->
                <div class="flex flex-col items-center gap-2">
                    <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold tracking-wide">Loading</p>
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-48 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full animate-pulse" style="width: 60%; animation: shimmer 2s infinite;"></div>
                </div>
            </div>

            <style>
                @keyframes shimmer {
                    0% { transform: translateX(-100%); }
                    100% { transform: translateX(100%); }
                }
            </style>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
               
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        <!-- Page Loader Script -->
        <script>
            (function() {
                const loader = document.getElementById('page-loader');

                // Show loader on link clicks (for navigation)
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a[href]');
                    if (link && !link.hasAttribute('download') && !link.getAttribute('href').startsWith('#') && !link.getAttribute('href').startsWith('javascript:')) {
                        // Check if it's an internal link
                        const href = link.getAttribute('href');
                        if (href.startsWith('/') || href.startsWith(window.location.origin)) {
                            loader.classList.remove('hidden');
                        }
                    }
                });

                // Show loader on form submits (for navigation)
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (form && (!form.hasAttribute('target') || form.getAttribute('target') !== '_blank')) {
                        loader.classList.remove('hidden');
                    }
                });

                // Hide loader when page is fully loaded
                window.addEventListener('load', function() {
                    loader.classList.add('hidden');
                });

                // Also hide loader on DOMContentLoaded as fallback
                document.addEventListener('DOMContentLoaded', function() {
                    // Small delay to ensure everything is rendered
                    setTimeout(() => {
                        loader.classList.add('hidden');
                    }, 100);
                });

                // Hide loader if user navigates back/forward
                window.addEventListener('pageshow', function(event) {
                    if (event.persisted) {
                        loader.classList.add('hidden');
                    }
                });
            })();
        </script>
    </body>
</html>
