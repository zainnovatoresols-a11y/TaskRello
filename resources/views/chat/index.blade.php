@extends('chat.layout')

@section('chat-content')

<aside id="conversation-sidebar"
       class="w-full max-w-[28rem] flex-shrink-0 bg-gradient-to-b from-white to-slate-50/50 dark:from-slate-950 dark:to-slate-900/50
              border-r border-slate-200/60 dark:border-slate-800/60
              flex flex-col h-full shadow-2xl backdrop-blur-2xl
              fixed lg:static inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0
              transition-transform duration-300 ease-in-out lg:transition-none">

    {{-- Sidebar header --}}
    <div class="px-4 sm:px-5 py-4 sm:py-5 border-b border-slate-200/50 dark:border-slate-700/50 bg-white/80 dark:bg-slate-950/80 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-base sm:text-lg tracking-tight">
                Messages
            </h3>
            {{-- New group button --}}
            <button onclick="openNewDirectModal()"
                    class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl
                           bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700
                           text-white shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                    title="New group conversation">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>

        {{-- Search conversations --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   id="conv-search"
                   placeholder="Search conversations..."
                   oninput="filterConversations(this.value)"
                   class="w-full bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/50 dark:border-slate-700/50
                          rounded-2xl pl-9 sm:pl-11 pr-4 py-2.5 sm:py-3 text-sm
                          text-slate-900 dark:text-slate-100
                          placeholder-slate-500 dark:placeholder-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                          transition-all duration-200 backdrop-blur-sm">
        </div>
    </div>

    {{-- Conversation list --}}
    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent" id="conversation-list">

        @forelse($conversations as $conv)
            <a href="{{ route('chat.show', $conv['id']) }}"
               id="conv-item-{{ $conv['id'] }}"
               class="conv-item flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3 sm:py-4 cursor-pointer
                      rounded-2xl sm:rounded-3xl mx-2 my-1 transition-all duration-200
                      hover:bg-slate-100/80 dark:hover:bg-slate-800/50 hover:shadow-md
                      border border-transparent hover:border-slate-200/50 dark:hover:border-slate-700/50
                      {{ isset($activeId) && $activeId == $conv['id']
                          ? 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border-blue-200 dark:border-blue-800 shadow-lg'
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
                                 class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl object-cover ring-2 ring-white dark:ring-slate-800 shadow-sm"
                                 alt="{{ $other['name'] }}">
                        @else
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center
                                        justify-center text-white font-bold text-xs sm:text-sm shadow-lg">
                                {{ strtoupper(substr($conv['name'], 0, 2)) }}
                            </div>
                        @endif
                    @elseif($conv['type'] === 'board')
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center
                                    justify-center text-white font-bold shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 flex items-center
                                    justify-center text-white font-bold shadow-lg">
                            {{ strtoupper(substr($conv['name'], 0, 2)) }}
                        </div>
                    @endif

                    {{-- Type badge --}}
                    @if($conv['type'] === 'group')
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 bg-green-500
                                    rounded-full border-2 border-white dark:border-slate-800
                                    flex items-center justify-center shadow-sm">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate max-w-[120px] sm:max-w-[160px]">
                            {{ $conv['name'] }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 flex-shrink-0 ml-2">
                            {{ $conv['last_message']
                                ? $conv['last_message']['created_at']
                                : '' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 truncate max-w-[140px] sm:max-w-[180px]">
                            @if($conv['last_message'])
                                @if($conv['type'] !== 'direct')
                                    <span class="font-medium text-slate-700 dark:text-slate-200">
                                        {{ $conv['last_message']['sender_name'] }}:
                                    </span>
                                @endif
                                {{ $conv['last_message']['body'] }}
                            @else
                                <span class="italic text-slate-500 dark:text-slate-400">No messages yet</span>
                            @endif
                        </p>
                        {{-- Unread badge --}}
                        @if($conv['unread_count'] > 0)
                            <span class="ml-2 flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 bg-gradient-to-r from-red-500 to-pink-500
                                         text-white text-xs font-bold rounded-full
                                         flex items-center justify-center shadow-lg animate-pulse"
                                  id="unread-{{ $conv['id'] }}">
                                {{ $conv['unread_count'] > 9 ? '9+' : $conv['unread_count'] }}
                            </span>
                        @else
                            <span class="hidden ml-2 flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 bg-gradient-to-r from-red-500 to-pink-500
                                         text-white text-xs font-bold rounded-full
                                         flex items-center justify-center shadow-lg"
                                  id="unread-{{ $conv['id'] }}">
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-16 sm:py-20 px-4 sm:px-6 text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 rounded-3xl
                            flex items-center justify-center mb-4 shadow-lg">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-slate-500" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                                 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                                 C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                                 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                    No conversations yet
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs leading-relaxed">
                    Start a chat with a team member to begin collaborating
                </p>
            </div>
        @endforelse
    </div>

    {{-- New direct message button --}}
    <div class="px-4 sm:px-5 py-3 sm:py-4 border-t border-slate-200/50 dark:border-slate-700/50 bg-white/60 dark:bg-slate-950/60 backdrop-blur-sm">
        <button onclick="openNewGroupModal()"
                class="w-full flex items-center justify-center gap-2 sm:gap-3
                       bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
                       text-white text-sm font-semibold py-2.5 sm:py-3 rounded-2xl
                       shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
             
        <!-- Group Chat Icon -->
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-1a4 4 0 00-5-3.87M17 20H7m10 0v-1c0-.65-.13-1.27-.37-1.82M7 20H2v-1a4 4 0 015-3.87M7 20v-1c0-.65.13-1.27.37-1.82m0 0a5 5 0 019.26 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
            Create a group conversation
        </button>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MAIN AREA — Active conversation or empty state            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<main class="flex-1 flex flex-col h-full bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 min-w-0 lg:ml-0">

    @if(isset($activeConversation))

        {{-- ── Conversation top bar ────────────────────────── --}}
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-white/90 dark:bg-slate-950/90
                    border-b border-slate-200/60 dark:border-slate-800/60
                    flex-shrink-0 shadow-lg backdrop-blur-xl">

            <div class="flex items-center gap-3 sm:gap-4">
                {{-- Mobile back button --}}
                <button onclick="closeMobileSidebar()"
                        class="lg:hidden text-slate-500 hover:text-slate-700 dark:hover:text-slate-200
                               transition-colors p-2 -ml-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Avatar --}}
                @php
                    $convName = $activeConversation->getDisplayNameFor(auth()->user());
                @endphp
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center
                            justify-center text-white font-bold text-xs sm:text-sm flex-shrink-0 shadow-lg">
                    {{ strtoupper(substr($convName, 0, 2)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-slate-900 dark:text-white text-sm sm:text-base truncate">
                        {{ $convName }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
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
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Participant avatars --}}
                <div class="flex -space-x-1 sm:-space-x-2 mr-2 sm:mr-3">
                    @foreach($activeConversation->users->take(4) as $p)
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 ring-2
                                    ring-white dark:ring-slate-800 flex items-center
                                    justify-center text-white text-xs font-bold shadow-md"
                             title="{{ $p->name }}">
                            {{ strtoupper(substr($p->name, 0, 1)) }}
                        </div>
                    @endforeach
                </div>

                {{-- Mute button --}}
                <button onclick="toggleMute({{ $activeConversation->id }})"
                        class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl
                               text-slate-500 hover:text-slate-700 dark:hover:text-slate-200
                               hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200
                               shadow-sm hover:shadow-md"
                        title="Mute conversation"
                        id="mute-btn">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                                 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2
                                 0 10-4 0v.341C7.67 6.165 6 8.388 6
                                 11v3.159c0 .538-.214 1.055-.595 1.436L4
                                 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                {{-- Add member (group only) --}}
                @if($activeConversation->type === 'group')
                    <button onclick="openAddMemberModal({{ $activeConversation->id }})"
                            class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl
                                   text-slate-500 hover:text-slate-700 dark:hover:text-slate-200
                                   hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200
                                   shadow-sm hover:shadow-md"
                            title="Add member">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0
                                     11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        {{-- ── Message thread ──────────────────────────────── --}}
        <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 sm:py-6 space-y-3 sm:space-y-4 bg-gradient-to-b from-white to-slate-50/50 dark:from-slate-900 dark:to-slate-800/50
                    rounded-2xl sm:rounded-[3rem] border border-slate-200/40 dark:border-slate-800/40 shadow-inner mx-2 my-2"
             id="message-thread"
             data-conversation-id="{{ $activeConversation->id }}">

            {{-- Load more button --}}
            <div class="text-center mb-4 sm:mb-6" id="load-more-wrap">
                <button onclick="loadMoreMessages()"
                        id="load-more-btn"
                        class="text-xs sm:text-sm text-blue-600 dark:text-blue-400
                               hover:text-blue-700 dark:hover:text-blue-300
                               hover:underline hidden font-medium transition-colors">
                    Load older messages
                </button>
            </div>

            {{-- Messages rendered by JS on load --}}
            <div id="messages-container" class="space-y-4">
                <div class="flex items-start gap-4 animate-pulse">
                    <div class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                    <div class="space-y-3 w-full max-w-2xl">
                        <div class="w-48 h-4 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                        <div class="w-full h-20 rounded-3xl bg-slate-200 dark:bg-slate-700"></div>
                    </div>
                </div>
                <div class="flex items-end justify-end gap-4 animate-pulse">
                    <div class="space-y-3 w-full max-w-2xl text-right">
                        <div class="mx-auto w-3/4 h-4 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                        <div class="mx-auto w-full h-20 rounded-3xl bg-slate-200 dark:bg-slate-700"></div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                </div>
                <div class="flex items-start gap-4 animate-pulse">
                    <div class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                    <div class="space-y-3 w-full max-w-2xl">
                        <div class="w-44 h-4 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                        <div class="w-5/6 h-20 rounded-3xl bg-slate-200 dark:bg-slate-700"></div>
                    </div>
                </div>
            </div>

            {{-- Typing indicator --}}
            <div id="typing-indicator" class="hidden flex items-center gap-3 py-2">
                <div class="flex gap-1.5 items-center bg-white/80 dark:bg-slate-800/80
                            rounded-2xl px-4 py-3 shadow-lg backdrop-blur-sm border border-slate-200/50 dark:border-slate-700/50">
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"
                          style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"
                          style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"
                          style="animation-delay: 300ms"></span>
                </div>
                <span class="text-sm text-slate-500 dark:text-slate-400 font-medium"
                      id="typing-text">
                </span>
            </div>
        </div>

        {{-- ── Reply preview bar ───────────────────────────── --}}
        <div id="reply-bar"
             class="hidden mx-2 sm:mx-6 mb-2 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30
                    border border-blue-200/50 dark:border-blue-800/50
                    rounded-2xl px-4 sm:px-5 py-3 sm:py-4 flex items-center
                    justify-between shadow-lg backdrop-blur-sm">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-semibold text-blue-700 dark:text-blue-400 mb-1">
                    Replying to
                    <span id="reply-to-name"></span>
                </p>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 truncate"
                   id="reply-to-body">
                </p>
            </div>
            <button onclick="cancelReply()"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                           text-xl sm:text-2xl ml-3 sm:ml-4 flex-shrink-0 hover:scale-110 transition-transform">
                &times;
            </button>
        </div>

        {{-- ── Message input ───────────────────────────────── --}}
        <div class="px-4 sm:px-6 py-3 sm:py-5 bg-white/90 dark:bg-slate-950/90
                    border-t border-slate-200/60 dark:border-slate-800/60
                    flex-shrink-0 shadow-2xl backdrop-blur-xl mx-2 mb-2 rounded-2xl sm:rounded-3xl">

            <div class="flex items-end gap-3 sm:gap-4">

                {{-- Attachment button --}}
                <label class="flex-shrink-0 w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center
                              rounded-2xl text-slate-500 hover:text-blue-600
                              dark:text-slate-300 dark:hover:text-blue-400 bg-slate-100
                              dark:bg-slate-900 hover:bg-blue-50 dark:hover:bg-blue-900/30
                              cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M15.172 7l-6.586 6.586a2 2 0 102.828
                                 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415
                                 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <input type="file"
                           id="chat-file-input"
                           class="hidden"
                           onchange="sendAttachment({{ $activeConversation->id }}, this)">
                </label>

                {{-- Textarea --}}
                <div class="flex-1 relative">
                    <textarea id="message-input"
                              placeholder="Type a message..."
                              rows="1"
                              data-conversation-id="{{ $activeConversation->id }}"
                              onkeydown="handleMessageKeydown(event, {{ $activeConversation->id }})"
                              oninput="handleTypingInput({{ $activeConversation->id }})"
                              class="w-full border border-slate-200/60 dark:border-slate-700/60
                                     rounded-2xl px-4 sm:px-5 py-3 sm:py-4 text-sm resize-none
                                     bg-white dark:bg-slate-900
                                     text-slate-900 dark:text-slate-100
                                     placeholder-slate-400 dark:placeholder-slate-500
                                     focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                                     transition-all duration-200 shadow-sm focus:shadow-lg
                                     max-h-24 sm:max-h-32 overflow-y-auto"></textarea>
                </div>

                {{-- Send button --}}
                <button onclick="sendMessage({{ $activeConversation->id }})"
                        class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center
                               bg-gradient-to-br from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
                               text-white rounded-2xl shadow-lg hover:shadow-xl
                               transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

    @else

        {{-- ── Empty state (no active conversation) ──────── --}}
        <div class="flex-1 flex flex-col items-center justify-center text-center px-6 sm:px-8">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-3xl
                        flex items-center justify-center mb-4 sm:mb-6 shadow-2xl">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-blue-500" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                             8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                             C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                             3.582 9 8z"/>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2 sm:mb-3">
                Your messages
            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 max-w-sm mb-6 sm:mb-8 leading-relaxed">
                Send private messages or start group conversations
                with your team members to collaborate effectively
            </p>
            <button onclick="openNewDirectModal()"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
                           text-white text-sm font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-2xl
                           shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                Start a conversation
            </button>
        </div>

    @endif
</main>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                     --}}
{{-- ══════════════════════════════════════════════════════════ --}}

{{-- New Direct Message Modal --}}
<div id="new-direct-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6"
     style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(8px);">
    <div class="bg-white/95 dark:bg-slate-800/95 rounded-2xl sm:rounded-3xl shadow-2xl w-full max-w-sm sm:max-w-md p-6 sm:p-8 backdrop-blur-2xl border border-white/20 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="font-bold text-slate-900 dark:text-white text-lg sm:text-xl">
                New message
            </h3>
            <button onclick="closeNewDirectModal()"
                    class="text-slate-400 hover:text-slate-600
                           dark:hover:text-slate-200 text-xl sm:text-2xl font-bold hover:scale-110 transition-transform">
                &times;
            </button>
        </div>

        {{-- User search --}}
        <div class="relative mb-4">
            <input type="text"
                   id="user-search-input"
                   placeholder="Search by name or email..."
                   oninput="searchUsers(this.value)"
                   class="w-full border border-slate-200/60 dark:border-slate-700/60
                          rounded-2xl px-4 sm:px-5 py-3 sm:py-4 text-sm
                          bg-white/80 dark:bg-slate-900/80
                          text-slate-900 dark:text-slate-100
                          placeholder-slate-500 dark:placeholder-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                          transition-all duration-200 backdrop-blur-sm shadow-sm">
        </div>

        {{-- Search results --}}
        <div id="user-search-results"
             class="max-h-60 sm:max-h-72 overflow-y-auto space-y-2 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-6 sm:py-8 italic">
                Start typing to search users
            </p>
        </div>
    </div>
</div>

{{-- New Group Modal --}}
<div id="new-group-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(8px);">
    <div class="bg-white/95 dark:bg-slate-800/95 rounded-3xl shadow-2xl w-full max-w-md p-8 backdrop-blur-2xl border border-white/20 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-900 dark:text-white text-xl">
                New group conversation
            </h3>
            <button onclick="closeNewGroupModal()"
                    class="text-slate-400 hover:text-slate-600
                           dark:hover:text-slate-200 text-2xl font-bold hover:scale-110 transition-transform">
                &times;
            </button>
        </div>

        <input type="text"
               id="group-name-input"
               placeholder="Group name..."
               maxlength="255"
               class="w-full border border-slate-200/60 dark:border-slate-700/60
                      rounded-2xl px-5 py-4 text-sm mb-4
                      bg-white/80 dark:bg-slate-900/80
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                      transition-all duration-200 backdrop-blur-sm shadow-sm">

        {{-- Selected members --}}
        <div id="selected-members"
             class="flex flex-wrap gap-2 mb-4 min-h-[40px] p-2 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50">
        </div>

        {{-- Member search --}}
        <input type="text"
               id="group-user-search"
               placeholder="Search and add members..."
               oninput="searchGroupUsers(this.value)"
               class="w-full border border-slate-200/60 dark:border-slate-700/60
                      rounded-2xl px-5 py-4 text-sm mb-4
                      bg-white/80 dark:bg-slate-900/80
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                      transition-all duration-200 backdrop-blur-sm shadow-sm">

        <div id="group-user-results"
             class="max-h-48 overflow-y-auto space-y-2 mb-6 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
        </div>

        <button onclick="createGroup()"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
                       text-white font-semibold py-4 rounded-2xl text-sm
                       shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
            Create group
        </button>
    </div>
</div>

{{-- Add Member Modal --}}
<div id="add-member-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(8px);">
    <div class="bg-white/95 dark:bg-slate-800/95 rounded-3xl shadow-2xl w-full max-w-sm p-8 backdrop-blur-2xl border border-white/20 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-900 dark:text-white text-xl">
                Add member
            </h3>
            <button onclick="closeAddMemberModal()"
                    class="text-slate-400 hover:text-slate-600
                           dark:hover:text-slate-200 text-2xl font-bold hover:scale-110 transition-transform">
                &times;
            </button>
        </div>
        <input type="hidden" id="add-member-conv-id">
        <input type="text"
               id="add-member-search"
               placeholder="Search users..."
               oninput="searchAddMember(this.value)"
               class="w-full border border-slate-200/60 dark:border-slate-700/60
                      rounded-2xl px-5 py-4 text-sm mb-4
                      bg-white/80 dark:bg-slate-900/80
                      text-slate-900 dark:text-slate-100
                      placeholder-slate-500 dark:placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                      transition-all duration-200 backdrop-blur-sm shadow-sm">
        <div id="add-member-results"
             class="max-h-64 overflow-y-auto space-y-2 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const CURRENT_USER_ID      = {{ auth()->id() }};
    const CURRENT_USER_NAME    = @json(auth()->user()->name);
    const ACTIVE_CONV_ID       = {{ isset($activeConversation) ? $activeConversation->id : 'null' }};
    const CSRF_TOKEN           = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script src="{{ asset('js/chat.js') }}?v={{ filemtime(public_path('js/chat.js')) }}"></script>
@endsection