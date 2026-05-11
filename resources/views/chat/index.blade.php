@extends('chat.layout')

@section('chat-content')

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- LEFT SIDEBAR — Conversation list                          --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<aside class="w-full max-w-[26rem] flex-shrink-0 bg-white/95 dark:bg-slate-950/95
              border-r border-slate-200/80 dark:border-slate-800
              flex flex-col h-full shadow-xl backdrop-blur-xl">

    {{-- Sidebar header --}}
    <div class="px-4 py-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-gray-900 dark:text-white text-base">
                Messages
            </h2>
            {{-- New group button --}}
            <button onclick="openNewGroupModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg
                           bg-blue-50 dark:bg-blue-900/30 text-blue-700
                           dark:text-blue-400 hover:bg-blue-100
                           dark:hover:bg-blue-900/50 transition"
                    title="New group conversation">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>

        {{-- Search conversations --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center
                        pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   id="conv-search"
                   placeholder="Search conversations..."
                   oninput="filterConversations(this.value)"
                   class="w-full bg-gray-100 dark:bg-gray-700 border-0
                          rounded-lg pl-9 pr-3 py-2 text-sm
                          text-gray-900 dark:text-gray-100
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    {{-- Conversation list --}}
    <div class="flex-1 overflow-y-auto" id="conversation-list">

        @forelse($conversations as $conv)
            <a href="{{ route('chat.show', $conv['id']) }}"
               id="conv-item-{{ $conv['id'] }}"
               class="conv-item flex items-center gap-3 px-4 py-3 cursor-pointer
                      rounded-3xl transition-all duration-200
                      hover:bg-slate-100 dark:hover:bg-slate-900
                      border border-transparent
                      {{ isset($activeId) && $activeId == $conv['id']
                          ? 'bg-slate-100 dark:bg-slate-900 border-slate-200 dark:border-slate-800 shadow-sm'
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
                                 class="w-10 h-10 rounded-full object-cover"
                                 alt="{{ $other['name'] }}">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center
                                        justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($conv['name'], 0, 2)) }}
                            </div>
                        @endif
                    @elseif($conv['type'] === 'board')
                        <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center
                                    justify-center text-white font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center
                                    justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($conv['name'], 0, 2)) }}
                        </div>
                    @endif

                    {{-- Type badge --}}
                    @if($conv['type'] === 'group')
                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500
                                    rounded-full border-2 border-white dark:border-gray-800
                                    flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-900
                                     dark:text-white truncate max-w-[140px]">
                            {{ $conv['name'] }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500
                                     flex-shrink-0 ml-1">
                            {{ $conv['last_message']
                                ? $conv['last_message']['created_at']
                                : '' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mt-0.5">
                        <p class="text-xs text-gray-500 dark:text-gray-400
                                  truncate max-w-[160px]">
                            @if($conv['last_message'])
                                @if($conv['type'] !== 'direct')
                                    <span class="font-medium">
                                        {{ $conv['last_message']['sender_name'] }}:
                                    </span>
                                @endif
                                {{ $conv['last_message']['body'] }}
                            @else
                                <span class="italic">No messages yet</span>
                            @endif
                        </p>
                        {{-- Unread badge --}}
                        @if($conv['unread_count'] > 0)
                            <span class="ml-1 flex-shrink-0 w-5 h-5 bg-blue-700
                                         text-white text-xs font-bold rounded-full
                                         flex items-center justify-center"
                                  id="unread-{{ $conv['id'] }}">
                                {{ $conv['unread_count'] > 9 ? '9+' : $conv['unread_count'] }}
                            </span>
                        @else
                            <span class="hidden ml-1 flex-shrink-0 w-5 h-5 bg-blue-700
                                         text-white text-xs font-bold rounded-full
                                         flex items-center justify-center"
                                  id="unread-{{ $conv['id'] }}">
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-full
                            flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                                 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                                 C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                                 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                    No conversations yet
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Start a chat with a team member
                </p>
            </div>
        @endforelse
    </div>

    {{-- New direct message button --}}
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
        <button onclick="openNewDirectModal()"
                class="w-full flex items-center justify-center gap-2
                       bg-blue-700 hover:bg-blue-800 text-white text-sm
                       font-medium py-2.5 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            New message
        </button>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MAIN AREA — Active conversation or empty state            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<main class="flex-1 flex flex-col h-full bg-slate-100 dark:bg-slate-950 min-w-0">

    @if(isset($activeConversation))

        {{-- ── Conversation top bar ────────────────────────── --}}
        <div class="flex items-center justify-between px-5 py-3 bg-white
                    dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800
                    flex-shrink-0 shadow-sm">

            <div class="flex items-center gap-3">
                {{-- Avatar --}}
                @php
                    $convName = $activeConversation->getDisplayNameFor(auth()->user());
                @endphp
                <div class="w-9 h-9 rounded-full bg-blue-700 flex items-center
                            justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($convName, 0, 2)) }}
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                        {{ $convName }}
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
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
            <div class="flex items-center gap-2">
                {{-- Participant avatars --}}
                <div class="flex -space-x-1.5 mr-2">
                    @foreach($activeConversation->users->take(4) as $p)
                        <div class="w-7 h-7 rounded-full bg-blue-600 ring-2
                                    ring-white dark:ring-gray-800 flex items-center
                                    justify-center text-white text-xs font-bold"
                             title="{{ $p->name }}">
                            {{ strtoupper(substr($p->name, 0, 1)) }}
                        </div>
                    @endforeach
                </div>

                {{-- Mute button --}}
                <button onclick="toggleMute({{ $activeConversation->id }})"
                        class="w-8 h-8 flex items-center justify-center rounded-lg
                               text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                        title="Mute conversation"
                        id="mute-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            title="Add member">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-1 bg-white dark:bg-slate-900
                    rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-inner"
             id="message-thread"
             data-conversation-id="{{ $activeConversation->id }}">

            {{-- Load more button --}}
            <div class="text-center mb-4" id="load-more-wrap">
                <button onclick="loadMoreMessages()"
                        id="load-more-btn"
                        class="text-xs text-blue-600 dark:text-blue-400
                               hover:underline hidden">
                    Load older messages
                </button>
            </div>

            {{-- Messages rendered by JS on load --}}
            <div id="messages-container" class="space-y-3">
                <div class="flex items-start gap-3 animate-pulse">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                    <div class="space-y-2 w-full max-w-2xl">
                        <div class="w-48 h-3 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        <div class="w-full h-16 rounded-3xl bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                </div>
                <div class="flex items-end justify-end gap-3 animate-pulse">
                    <div class="space-y-2 w-full max-w-2xl text-right">
                        <div class="mx-auto w-3/4 h-3 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        <div class="mx-auto w-full h-16 rounded-3xl bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                </div>
                <div class="flex items-start gap-3 animate-pulse">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                    <div class="space-y-2 w-full max-w-2xl">
                        <div class="w-40 h-3 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                        <div class="w-4/5 h-16 rounded-3xl bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                </div>
            </div>

            {{-- Typing indicator --}}
            <div id="typing-indicator" class="hidden flex items-center gap-2 py-1">
                <div class="flex gap-1 items-center bg-white dark:bg-gray-800
                            rounded-2xl px-3 py-2 shadow-sm">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"
                          style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"
                          style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"
                          style="animation-delay: 300ms"></span>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500"
                      id="typing-text">
                </span>
            </div>
        </div>

        {{-- ── Reply preview bar ───────────────────────────── --}}
        <div id="reply-bar"
             class="hidden mx-5 mb-0 bg-slate-50 dark:bg-slate-900/80
                    border border-slate-200 dark:border-slate-800
                    rounded-2xl px-4 py-3 flex items-center
                    justify-between shadow-sm">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-0.5">
                    Replying to
                    <span id="reply-to-name"></span>
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-300 truncate"
                   id="reply-to-body">
                </p>
            </div>
            <button onclick="cancelReply()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           text-xl ml-3 flex-shrink-0">
                &times;
            </button>
        </div>

        {{-- ── Message input ───────────────────────────────── --}}
        <div class="px-5 py-4 bg-slate-50 dark:bg-slate-950
                    border-t border-slate-200 dark:border-slate-800
                    flex-shrink-0 shadow-lg">

            <div class="flex items-end gap-3">

                {{-- Attachment button --}}
                <label class="flex-shrink-0 w-9 h-9 flex items-center justify-center
                              rounded-2xl text-slate-500 hover:text-blue-600
                              dark:text-slate-300 dark:hover:text-blue-400 bg-slate-100
                              dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800
                              cursor-pointer transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                              placeholder="Type a message... (Enter to send, Shift+Enter for new line)"
                              rows="1"
                              data-conversation-id="{{ $activeConversation->id }}"
                              onkeydown="handleMessageKeydown(event, {{ $activeConversation->id }})"
                              oninput="handleTypingInput({{ $activeConversation->id }})"
                              class="w-full border border-slate-200 dark:border-slate-800
                                     rounded-2xl px-4 py-3 text-sm resize-none
                                     bg-white dark:bg-slate-900
                                     text-slate-900 dark:text-slate-100
                                     placeholder-slate-400 dark:placeholder-slate-500
                                     focus:outline-none focus:ring-2 focus:ring-blue-500
                                     focus:border-transparent transition
                                     max-h-32 overflow-y-auto">
                    </textarea>
                </div>

                {{-- Send button --}}
                <button onclick="sendMessage({{ $activeConversation->id }})"
                        class="flex-shrink-0 w-11 h-11 flex items-center justify-center
                               bg-gradient-to-br from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600
                               text-white rounded-2xl shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

    @else

        {{-- ── Empty state (no active conversation) ──────── --}}
        <div class="flex-1 flex flex-col items-center justify-center
                    text-center px-8">
            <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/30 rounded-full
                        flex items-center justify-center mb-5">
                <svg class="w-10 h-10 text-blue-400" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03
                             8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                             C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9
                             3.582 9 8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-2">
                Your messages
            </h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs mb-6">
                Send private messages or start group conversations
                with your team members
            </p>
            <button onclick="openNewDirectModal()"
                    class="bg-blue-700 hover:bg-blue-800 text-white text-sm
                           font-medium px-5 py-2.5 rounded-xl transition">
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
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.55);">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900 dark:text-white text-base">
                New message
            </h3>
            <button onclick="closeNewDirectModal()"
                    class="text-gray-400 hover:text-gray-600
                           dark:hover:text-gray-200 text-2xl font-bold">
                &times;
            </button>
        </div>

        {{-- User search --}}
        <div class="relative mb-3">
            <input type="text"
                   id="user-search-input"
                   placeholder="Search by name or email..."
                   oninput="searchUsers(this.value)"
                   class="w-full border border-gray-300 dark:border-gray-600
                          rounded-xl px-4 py-2.5 text-sm
                          bg-white dark:bg-gray-900
                          text-gray-900 dark:text-gray-100
                          placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          focus:border-transparent">
        </div>

        {{-- Search results --}}
        <div id="user-search-results"
             class="max-h-64 overflow-y-auto space-y-1">
            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6 italic">
                Start typing to search users
            </p>
        </div>
    </div>
</div>

{{-- New Group Modal --}}
<div id="new-group-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.55);">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900 dark:text-white text-base">
                New group conversation
            </h3>
            <button onclick="closeNewGroupModal()"
                    class="text-gray-400 hover:text-gray-600
                           dark:hover:text-gray-200 text-2xl font-bold">
                &times;
            </button>
        </div>

        <input type="text"
               id="group-name-input"
               placeholder="Group name..."
               maxlength="255"
               class="w-full border border-gray-300 dark:border-gray-600
                      rounded-xl px-4 py-2.5 text-sm mb-3
                      bg-white dark:bg-gray-900
                      text-gray-900 dark:text-gray-100
                      placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                      focus:border-transparent">

        {{-- Selected members --}}
        <div id="selected-members"
             class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
        </div>

        {{-- Member search --}}
        <input type="text"
               id="group-user-search"
               placeholder="Search and add members..."
               oninput="searchGroupUsers(this.value)"
               class="w-full border border-gray-300 dark:border-gray-600
                      rounded-xl px-4 py-2.5 text-sm mb-3
                      bg-white dark:bg-gray-900
                      text-gray-900 dark:text-gray-100
                      placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                      focus:border-transparent">

        <div id="group-user-results"
             class="max-h-40 overflow-y-auto space-y-1 mb-4">
        </div>

        <button onclick="createGroup()"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white
                       font-medium py-2.5 rounded-xl text-sm transition">
            Create group
        </button>
    </div>
</div>

{{-- Add Member Modal --}}
<div id="add-member-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.55);">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900 dark:text-white text-base">
                Add member
            </h3>
            <button onclick="closeAddMemberModal()"
                    class="text-gray-400 hover:text-gray-600
                           dark:hover:text-gray-200 text-2xl font-bold">
                &times;
            </button>
        </div>
        <input type="hidden" id="add-member-conv-id">
        <input type="text"
               id="add-member-search"
               placeholder="Search users..."
               oninput="searchAddMember(this.value)"
               class="w-full border border-gray-300 dark:border-gray-600
                      rounded-xl px-4 py-2.5 text-sm mb-3
                      bg-white dark:bg-gray-900
                      text-gray-900 dark:text-gray-100
                      placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                      focus:border-transparent">
        <div id="add-member-results"
             class="max-h-56 overflow-y-auto space-y-1">
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