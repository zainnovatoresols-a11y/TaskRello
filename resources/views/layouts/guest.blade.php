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
<style>
    .loader {
        display: flex;
        align-items: center;
    }

    .bar {
        display: inline-block;
        width: 3px;
        height: 20px;
        background-color: rgba(255, 255, 255, .5);
        border-radius: 10px;
        animation: scale-up4 1s linear infinite;
    }

    .bar:nth-child(2) {
        height: 35px;
        margin: 0 5px;
        animation-delay: .25s;
    }

    .bar:nth-child(3) {
        animation-delay: .5s;
    }

    @keyframes scale-up4 {
        20% {
            background-color: #ffff;
            transform: scaleY(1.5);
        }
        40% {
            transform: scaleY(1);
        }
    }
</style>

<div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-gray-900/85 backdrop-blur-sm"></div>
    <div class="relative flex flex-col items-center gap-4">
        <div class="loader">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <p style="color: rgba(255,255,255,0.5); font-size: 13px;
                  font-family: sans-serif; letter-spacing: 0.05em;">
        </p>
    </div>
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
                        // Skip loader if form has data-skip-loader attribute
                        if (!form.hasAttribute('data-skip-loader')) {
                            loader.classList.remove('hidden');
                        }
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
