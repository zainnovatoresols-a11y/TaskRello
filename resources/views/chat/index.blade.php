@extends('chat.layout')

@section('chat-content')

<aside id="conversation-sidebar"
    class="w-full max-w-[22rem] flex-shrink-0
              bg-white dark:bg-slate-950
              border-r border-slate-200/60 dark:border-slate-800/60
              flex flex-col h-full
              fixed lg:static inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0
              transition-transform duration-300 ease-in-out lg:transition-none">

    {{-- Sidebar header — search + plus button in one row --}}
    <div class="h-[56px] px-1 flex items-center gap-2
            border-b border-slate-200/50 dark:border-slate-700/50
            bg-white/80 dark:bg-slate-950/80 backdrop-blur-sm flex-shrink-0">

        {{-- Search bar --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                id="conv-search"
                placeholder="Search conversations..."
                oninput="filterConversations(this.value)"
                class="w-full bg-slate-100/80 dark:bg-slate-800/80
                      border border-slate-200/50 dark:border-slate-700/50
                      rounded-lg pl-9 pr-3 py-2 text-xs
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/40
                      transition-all duration-200">
        </div>

        {{-- Plus button --}}
        <button onclick="openNewDirectModal()"
            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                   bg-blue-600 hover:bg-blue-700
                   text-white transition-colors duration-200"
            title="New message">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>
    {{-- Conversation list --}}
    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent py-1"
        id="conversation-list">

        @forelse($conversations as $conv)
        <a href="{{ route('chat.show', $conv['id']) }}"
            id="conv-item-{{ $conv['id'] }}"
            class="conv-item flex items-center gap-3 px-3 py-2.5 cursor-pointer
                      px-3 my-0.5 rounded-xl transition-all duration-200
                      hover:bg-slate-100/80 dark:hover:bg-slate-800/50
                      border border-transparent hover:border-slate-200/50 dark:hover:border-slate-700/50
                      {{ isset($activeId) && $activeId == $conv['id']
                          ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-200/60 dark:border-blue-800/60'
                          : '' }}"
            data-name="{{ strtolower($conv['name']) }}">

            {{-- Avatar --}}
            <div class="relative flex-shrink-0">
                @if($conv['type'] === 'direct' && count($conv['participants']) > 0)
                @php
                $other = collect($conv['participants'])
                ->firstWhere('id', '!=', auth()->id());
                @endphp
                @if($other && !empty($other['avatar']))
                <img src="{{ asset('storage/' . $other['avatar']) }}"
                    class="w-9 h-9 rounded-xl object-cover ring-2 ring-white dark:ring-slate-800"
                    alt="{{ $other['name'] }}">
                @else
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center
                                        justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr($conv['name'], 0, 2)) }}
                </div>
                @endif
                @elseif($conv['type'] === 'board')
                <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center
                                    justify-center text-white font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                    </svg>
                </div>
                @else
                <div class="w-9 h-9 rounded-xl bg-green-600 flex items-center
                                    justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr($conv['name'], 0, 2)) }}
                </div>
                @endif

                {{-- Type badge --}}
                @if($conv['type'] === 'group')
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500
                                    rounded-full border-2 border-white dark:border-slate-800
                                    flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-0.5">
                    <span class="text-xs font-semibold text-slate-900 dark:text-white truncate max-w-[130px]">
                        {{ $conv['name'] }}
                    </span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 flex-shrink-0 ml-1">
                        {{ $conv['last_message'] ? $conv['last_message']['created_at'] : '' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[140px]">
                        @if($conv['last_message'])
                        @if($conv['type'] !== 'direct')
                        <span class="font-medium text-slate-600 dark:text-slate-300">
                            {{ $conv['last_message']['sender_name'] }}:
                        </span>
                        @endif
                        {{ $conv['last_message']['body'] }}
                        @else
                        <span class="italic text-slate-400 dark:text-slate-500">No messages yet</span>
                        @endif
                    </p>
                    {{-- Unread badge --}}
                    @if($conv['unread_count'] > 0)
                    <span class="ml-1.5 flex-shrink-0 w-4 h-4 bg-red-500
                                         text-white text-[10px] font-bold rounded-full
                                         flex items-center justify-center"
                        id="unread-{{ $conv['id'] }}">
                        {{ $conv['unread_count'] > 9 ? '9+' : $conv['unread_count'] }}
                    </span>
                    @else
                    <span class="hidden ml-1.5 flex-shrink-0 w-4 h-4 bg-red-500
                                         text-white text-[10px] font-bold rounded-full
                                         flex items-center justify-center"
                        id="unread-{{ $conv['id'] }}">
                    </span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-2xl
                            flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                                 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                                 C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                                 3.582 9 8z" />
                </svg>
            </div>
            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                No conversations yet
            </p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 max-w-xs leading-relaxed">
                Start a chat with a team member to begin collaborating
            </p>
        </div>
        @endforelse
    </div>

    {{-- Sidebar footer — fixed height min-h-[64px] to match main input bar --}}
    <div class="min-h-[64px] px-3 flex items-center
                border-t border-slate-200/50 dark:border-slate-700/50
                bg-white/60 dark:bg-slate-950/60 backdrop-blur-sm flex-shrink-0">
        <button onclick="openNewGroupModal()"
            class="w-full flex h-11 w-11 items-center justify-center gap-2
                       bg-blue-600 hover:bg-blue-700
                       text-white text-xs font-semibold py-2.5 rounded-xl
                       transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-1a4 4 0 00-5-3.87M17 20H7m10 0v-1c0-.65-.13-1.27-.37-1.82M7 20H2v-1a4 4 0 015-3.87M7 20v-1c0-.65.13-1.27.37-1.82m0 0a5 5 0 019.26 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Create a group conversation
        </button>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MAIN AREA — Active conversation or empty state            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<main class="flex-1 flex flex-col h-full bg-slate-50 dark:bg-slate-900 min-w-0 lg:ml-0">

    @if(isset($activeConversation))

    {{-- ── Conversation top bar — fixed height h-[72px] to match sidebar header --}}
    <div class="h-[56px] flex items-center justify-between px-4 sm:px-5
                    bg-white/90 dark:bg-slate-950/90
                    border-b border-slate-200/60 dark:border-slate-800/60
                    flex-shrink-0 backdrop-blur-xl">

        <div class="flex items-center gap-3">
            {{-- Mobile back button --}}
            <button onclick="closeMobileSidebar()"
                class="lg:hidden text-slate-500 hover:text-slate-700 dark:hover:text-slate-200
                               transition-colors p-1 -ml-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Avatar --}}
            @php
            $convName = $activeConversation->getDisplayNameFor(auth()->user());
            @endphp
            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center
                            justify-center text-white font-bold text-xs flex-shrink-0">
                {{ strtoupper(substr($convName, 0, 2)) }}
            </div>

            <div class="min-w-0">
                <h3 class="font-semibold text-slate-900 dark:text-white text-sm truncate">
                    {{ $convName }}
                </h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    @if($activeConversation->type === 'direct')
                    Direct message
                    @elseif($activeConversation->type === 'board')
                    Board conversation
                    @else
                    {{ $activeConversation->users->count() }} members
                    @endif
                </p>
            </div>
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-1.5">
            {{-- Participant avatars --}}
            <div class="flex -space-x-1.5 mr-2">
                @foreach($activeConversation->users->take(4) as $p)
                <div class="w-7 h-7 rounded-lg bg-blue-600 ring-2
                                    ring-white dark:ring-slate-900 flex items-center
                                    justify-center text-white text-[10px] font-bold"
                    title="{{ $p->name }}">
                    {{ strtoupper(substr($p->name, 0, 1)) }}
                </div>
                @endforeach
            </div>

            {{-- Mute button --}}
            <button onclick="toggleMute({{ $activeConversation->id }})"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                               text-slate-500 hover:text-slate-700 dark:hover:text-slate-200
                               hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-200"
                title="Mute conversation"
                id="mute-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                                 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2
                                 0 10-4 0v.341C7.67 6.165 6 8.388 6
                                 11v3.159c0 .538-.214 1.055-.595 1.436L4
                                 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            {{-- Add member (group only) --}}
            @if($activeConversation->type === 'group')
            <button onclick="openAddMemberModal({{ $activeConversation->id }})"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-slate-500 hover:text-slate-700 dark:hover:text-slate-200
                                   hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-200"
                title="Add member">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0
                                     11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </button>
            @endif
        </div>
    </div>

    {{-- ── Message thread ──────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto px-4 scrollbar scrollbar-track-slate-950 scrollbar-thumb-slate-800 sm:px-5 py-4 space-y-3
                    bg-white dark:bg-slate-900
                    border-x border-slate-200/40 dark:border-slate-800/40"
        id="message-thread"
        data-conversation-id="{{ $activeConversation->id }}">

        {{-- Load more button --}}
        <div class="text-center mb-4" id="load-more-wrap">
            <button onclick="loadMoreMessages()"
                id="load-more-btn"
                class="text-xs text-blue-600 dark:text-blue-400
                               hover:text-blue-700 dark:hover:text-blue-300
                               hover:underline hidden font-medium transition-colors">
                Load older messages
            </button>
        </div>

        {{-- Messages rendered by JS on load --}}
        <div id="messages-container" class="space-y-3">
            <div class="flex items-start gap-3 animate-pulse">
                <div class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700 flex-shrink-0"></div>
                <div class="space-y-2 w-full max-w-md">
                    <div class="w-32 h-3 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                    <div class="w-full h-14 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                </div>
            </div>
            <div class="flex items-end justify-end gap-3 animate-pulse">
                <div class="space-y-2 w-full max-w-md text-right">
                    <div class="ml-auto w-3/4 h-3 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                    <div class="ml-auto w-full h-14 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700 flex-shrink-0"></div>
            </div>
            <div class="flex items-start gap-3 animate-pulse">
                <div class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700 flex-shrink-0"></div>
                <div class="space-y-2 w-full max-w-md">
                    <div class="w-28 h-3 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                    <div class="w-5/6 h-14 rounded-xl bg-slate-200 dark:bg-slate-700"></div>
                </div>
            </div>
        </div>

        {{-- Typing indicator --}}
        <div id="typing-indicator" class="hidden flex items-center gap-2 py-1">
            <div class="flex gap-1 items-center bg-slate-100 dark:bg-slate-800
                            rounded-xl px-3 py-2 border border-slate-200/50 dark:border-slate-700/50">
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"
                    style="animation-delay: 0ms"></span>
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"
                    style="animation-delay: 150ms"></span>
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"
                    style="animation-delay: 300ms"></span>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium"
                id="typing-text"></span>
        </div>
    </div>

    {{-- ── Reply preview bar ───────────────────────────── --}}
    <div id="reply-bar"
        class="hidden mx-3 mb-1.5
                    bg-blue-50 dark:bg-blue-900/20
                    border border-blue-200/50 dark:border-blue-800/50
                    rounded-xl px-4 py-2.5 flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-0.5">
                Replying to <span id="reply-to-name"></span>
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400 truncate"
                id="reply-to-body"></p>
        </div>
        <button onclick="cancelReply()"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                           text-lg ml-3 flex-shrink-0 transition-colors">
            &times;
        </button>
    </div>

    {{-- ── Message input bar ─────────────────────────────── --}}
    <div class="min-h-[64px] px-3 sm:px-4 flex items-center gap-2.5
                    bg-white/90 dark:bg-slate-950/90
                    border-t border-slate-200/60 dark:border-slate-800/60
                    flex-shrink-0 backdrop-blur-xl ">

        {{-- Attachment button --}}
        <label class="flex-shrink-0 w-11 h-11 flex items-center justify-center
                          rounded-lg text-slate-500 hover:text-blue-600
                          dark:text-slate-300 dark:hover:text-blue-400
                          bg-slate-100 dark:bg-slate-900
                          hover:bg-blue-50 dark:hover:bg-blue-900/30
                          cursor-pointer transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.172 7l-6.586 6.586a2 2 0 102.828
                             2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415
                             6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
            <input type="file"
                id="chat-file-input"
                class="hidden"
                onchange="sendAttachment({{ $activeConversation->id }}, this)">
        </label>

        {{-- Textarea --}}
        <div class="flex-1 mt-1.5">
            <textarea id="message-input"
                placeholder="Type a message..."
                rows="1"
                data-conversation-id="{{ $activeConversation->id }}"
                onkeydown="handleMessageKeydown(event, {{ $activeConversation->id }})"
                oninput="handleTypingInput({{ $activeConversation->id }})"
                class="w-full border scrollbar scrollbar-track-slate-950 scrollbar-thumb-slate-800 border-slate-200/60 dark:border-slate-700/60
                                 rounded-xl px-3 py-3 text-sm resize-none
                                 bg-slate-50 dark:bg-slate-900
                                 text-slate-900 dark:text-slate-100
                                 placeholder-slate-400 dark:placeholder-slate-500
                                 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                 transition-all duration-200
                                 max-h-32 overflow-y-auto leading-snug"></textarea>
        </div>

        {{-- Send button --}}
        <button onclick="sendMessage({{ $activeConversation->id }})"
            class="flex-shrink-0 w-11 h-11 flex items-center justify-center
               bg-blue-600 hover:bg-blue-700
               text-white rounded-lg transition-colors duration-200 ml-auto">
            <svg class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>

    @else

    {{-- ── Empty state (no active conversation) ──────── --}}
    <div class="flex-1 flex flex-col items-center justify-center text-center px-6 sm:px-8">
        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl
                        flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                             8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                             C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                             3.582 9 8z" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">
            Your messages
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xs mb-6 leading-relaxed">
            Send private messages or start group conversations
            with your team members to collaborate effectively
        </p>
        <button onclick="openNewDirectModal()"
            class="bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold px-5 py-2.5 rounded-xl
                           transition-colors duration-200">
            Start a conversation
        </button>
    </div>

    @endif
</main>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODALS  (zero logic changes — only cosmetic classes)      --}}
{{-- ══════════════════════════════════════════════════════════ --}}

{{-- New Direct Message Modal --}}
<div id="new-direct-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
    style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(6px);">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6
                border border-slate-200/50 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                New message
            </h3>
            <button onclick="closeNewDirectModal()"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                           text-xl font-bold transition-colors">
                &times;
            </button>
        </div>

        {{-- User search --}}
        <div class="relative mb-3">
            <input type="text"
                id="user-search-input"
                placeholder="Search by name or email..."
                oninput="searchUsers(this.value)"
                class="w-full border border-slate-200/60 dark:border-slate-700/60
                          rounded-xl px-4 py-3 text-sm
                          bg-slate-50 dark:bg-slate-900
                          text-slate-900 dark:text-slate-100
                          placeholder-slate-500 dark:placeholder-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500/40
                          transition-all duration-200">
        </div>

        {{-- Search results --}}
        <div id="user-search-results"
            class="max-h-64 overflow-y-auto space-y-1.5 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-8 italic">
                Start typing to search users
            </p>
        </div>
    </div>
</div>

{{-- New Group Modal --}}
<div id="new-group-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
    style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(6px);">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6
                border border-slate-200/50 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                New group conversation
            </h3>
            <button onclick="closeNewGroupModal()"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                           text-xl font-bold transition-colors">
                &times;
            </button>
        </div>

        <input type="text"
            id="group-name-input"
            placeholder="Group name..."
            maxlength="255"
            class="w-full border border-slate-200/60 dark:border-slate-700/60
                      rounded-xl px-4 py-3 text-sm mb-3
                      bg-slate-50 dark:bg-slate-900
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/40
                      transition-all duration-200">

        {{-- Selected members --}}
        <div id="selected-members"
            class="flex flex-wrap gap-1.5 mb-3 min-h-[36px] p-2 rounded-xl
                    bg-slate-50 dark:bg-slate-900/50
                    border border-slate-200/50 dark:border-slate-700/50">
        </div>

        {{-- Member search --}}
        <input type="text"
            id="group-user-search"
            placeholder="Search and add members..."
            oninput="searchGroupUsers(this.value)"
            class="w-full border border-slate-200/60 dark:border-slate-700/60
                      rounded-xl px-4 py-3 text-sm mb-3
                      bg-slate-50 dark:bg-slate-900
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/40
                      transition-all duration-200">

        <div id="group-user-results"
            class="max-h-40 overflow-y-auto space-y-1.5 mb-4 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
        </div>

        <button onclick="createGroup()"
            class="w-full bg-blue-600 hover:bg-blue-700
                       text-white font-semibold py-3 rounded-xl text-sm
                       transition-colors duration-200">
            Create group
        </button>
    </div>
</div>

{{-- Add Member Modal --}}
<div id="add-member-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
    style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(6px);">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6
                border border-slate-200/50 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                Add member
            </h3>
            <button onclick="closeAddMemberModal()"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                           text-xl font-bold transition-colors">
                &times;
            </button>
        </div>
        <input type="hidden" id="add-member-conv-id">
        <input type="text"
            id="add-member-search"
            placeholder="Search users..."
            oninput="searchAddMember(this.value)"
            class="w-full border border-slate-200/60 dark:border-slate-700/60
                      rounded-xl px-4 py-3 text-sm mb-3
                      bg-slate-50 dark:bg-slate-900
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/40
                      transition-all duration-200">
        <div id="add-member-results"
            class="max-h-56 overflow-y-auto space-y-1.5 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const CURRENT_USER_ID = {{ auth()->id() }};
    const CURRENT_USER_NAME = @json(auth()->user()->name);
    const ACTIVE_CONV_ID = {{ isset($activeConversation) ? $activeConversation->id : 'null' }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script src="{{ asset('js/chat.js') }}?v={{ filemtime(public_path('js/chat.js')) }}"></script>
@endsection