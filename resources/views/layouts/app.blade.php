<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Trello Clone') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/echo.js'])
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
                {{-- ── Chat icon ────────────────────────────────────────────── --}}
                <a href="{{ route('chat.index') }}"
                    class="relative flex items-center justify-center
          w-9 h-9 rounded-full text-white/80 hover:text-white
          hover:bg-white/10 transition"
                    title="Messages">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                 C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                 3.582 9 8z" />
                    </svg>

                    {{-- Unread badge --}}
                    @php
                    $totalUnread = auth()->user()->totalUnreadCount();
                    @endphp
                    @if($totalUnread > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500
                     text-white text-xs font-bold rounded-full
                     flex items-center justify-center leading-none"
                        id="chat-unread-badge">
                        {{ $totalUnread > 9 ? '9+' : $totalUnread }}
                    </span>
                    @else
                    <span class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500
                     text-white text-xs font-bold rounded-full
                     flex items-center justify-center leading-none"
                        id="chat-unread-badge">
                    </span>
                    @endif
                </a>
                <div x-data="{ open: false }" class="relative">

                    <button @click="open = !open; window.notificationsOpen = open; if(open) loadNotifications()"
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
                        @click.away="open = false; window.notificationsOpen = false"
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
                        @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}"
                            alt="{{ auth()->user()->name }}"
                            class="w-8 h-8 rounded-full ring-2 ring-white/30 object-cover">
                        @else
                        <span class="w-8 h-8 rounded-full bg-white/25 flex items-center justify-center font-bold text-xs ring-2 ring-white/30">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                        @endif
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

                        <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-2">
                                <button id="theme-light-btn" type="button" aria-label="Light mode"
                                    class="w-full h-10 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m0-12.728l1.414 1.414M18.364 18.364l-1.414-1.414M12 7a5 5 0 100 10 5 5 0 000-10z" />
                                    </svg>
                                </button>
                                <button id="theme-system-btn" type="button" aria-label="System mode"
                                    class="w-full h-10 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-4M9 3h6v4H9V3zm0 14h6v4H9v-4zm-3-7h14" />
                                    </svg>
                                </button>
                                <button id="theme-dark-btn" type="button" aria-label="Dark mode"
                                    class="w-full h-10 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

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

    <!-- Page Loader -->
    <div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <!-- Backdrop with blur -->
        <div class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm"></div>

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
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }
        </style>
    </div>

    <!-- Warning Confirmation Modal -->
    <div id="warning-modal" class="fixed inset-0 z-50 hidden" x-data="warningModal()" x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click.self="close()"></div>

        <!-- Modal Container -->
        <div class="relative flex items-center justify-center min-h-screen p-4" @click.stop>
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden" x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                <!-- Header -->
                <div class="flex items-center gap-3 p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0 w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="title">Warning</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="subtitle">Please confirm your action</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed" x-text="message">Are you sure you want to proceed?</p>

                    <!-- Additional Info (optional) -->
                    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800/30" x-show="warningText" x-transition>
                        <p class="text-sm text-amber-800 dark:text-amber-200" x-text="warningText"></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" @click.stop="close()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200" x-text="cancelText">Cancel</button>
                    <button type="button" @click.stop="confirm()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center gap-2" :class="typeof confirming !== 'undefined' && confirming ? 'opacity-75 cursor-not-allowed' : ''">
                        <svg x-show="typeof confirming !== 'undefined' && confirming" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span x-text="confirmText">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

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
    <script>
        (function() {
            const themeKey = 'taskrelloThemeMode';
            const root = document.documentElement;
            const lightButton = document.getElementById('theme-light-btn');
            const systemButton = document.getElementById('theme-system-btn');
            const darkButton = document.getElementById('theme-dark-btn');
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

            function getSavedTheme() {
                return localStorage.getItem(themeKey) || 'system';
            }

            function applyTheme(mode) {
                const isSystemDark = mediaQuery.matches;
                if (mode === 'dark' || (mode === 'system' && isSystemDark)) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
                updateActiveButton(mode);
            }

            function updateActiveButton(mode) {
                [lightButton, systemButton, darkButton].forEach((button) => {
                    button.classList.remove('bg-white/15', 'text-white', 'text-black');
                });

                const active = {
                    light: lightButton,
                    system: systemButton,
                    dark: darkButton,
                } [mode] || systemButton;

                active.classList.add('bg-white/15', 'text-black');
            }

            function setTheme(mode) {
                localStorage.setItem(themeKey, mode);
                applyTheme(mode);
            }

            function handleSystemChange() {
                if (getSavedTheme() === 'system') {
                    applyTheme('system');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                if (lightButton) lightButton.addEventListener('click', () => setTheme('light'));
                if (systemButton) systemButton.addEventListener('click', () => setTheme('system'));
                if (darkButton) darkButton.addEventListener('click', () => setTheme('dark'));
                applyTheme(getSavedTheme());
                if (mediaQuery.addEventListener) {
                    mediaQuery.addEventListener('change', handleSystemChange);
                } else if (mediaQuery.addListener) {
                    mediaQuery.addListener(handleSystemChange);
                }
            });
        })();
    </script>

    <!-- Warning Modal Script -->
    <script>
        function warningModal() {
            return {
                isOpen: false,
                title: 'Warning',
                subtitle: 'Please confirm your action',
                message: 'Are you sure you want to proceed?',
                warningText: '',
                cancelText: 'Cancel',
                confirmText: 'Confirm',
                confirming: false,
                resolve: null,

                show(options = {}) {
                    this.title = options.title || 'Warning';
                    this.subtitle = options.subtitle || 'Please confirm your action';
                    this.message = options.message || 'Are you sure you want to proceed?';
                    this.warningText = options.warningText || '';
                    this.cancelText = options.cancelText || 'Cancel';
                    this.confirmText = options.confirmText || 'Confirm';
                    this.isOpen = true;

                    return new Promise((resolve) => {
                        this.resolve = resolve;
                    });
                },

                confirm() {
                    this.confirming = true;
                    this.isOpen = false;
                    if (this.resolve) {
                        this.resolve(true);
                        this.resolve = null;
                    }
                    // Reset after animation
                    setTimeout(() => {
                        this.confirming = false;
                        this.reset();
                    }, 300);
                },

                close() {
                    this.isOpen = false;
                    if (this.resolve) {
                        this.resolve(false);
                        this.resolve = null;
                    }
                    // Reset after animation
                    setTimeout(() => {
                        this.reset();
                    }, 300);
                },

                reset() {
                    this.title = 'Warning';
                    this.subtitle = 'Please confirm your action';
                    this.message = 'Are you sure you want to proceed?';
                    this.warningText = '';
                    this.cancelText = 'Cancel';
                    this.confirmText = 'Confirm';
                    this.confirming = false;
                    this.resolve = null;
                }
            };
        }

        // Make it globally available
        window.warningModal = warningModal;
    </script>

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

    @yield('scripts')

</body>

</html>