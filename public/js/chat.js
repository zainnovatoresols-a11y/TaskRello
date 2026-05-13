// ============================================================
// CHAT.JS — Real-time chat with Laravel Echo + Reverb
// ============================================================

const chatCsrf = document.querySelector('meta[name="csrf-token"]').content;
let replyToId = null;
let typingTimer = null;
let isTyping = false;
let oldestMessageId = null;
let hasMoreMessages = true;
let echoChannel = null;
const groupSelectedUsers = {};

// ============================================================
// THEME MANAGEMENT — Light/Dark/System Mode
// ============================================================

const THEME_KEY = 'taskrelloThemeMode';  // Use same key as app layout
let currentTheme = 'system';
let systemThemeListener = null;

// Initialize theme on page load
function initializeTheme() {
    const savedTheme = localStorage.getItem(THEME_KEY) || 'system';
    setTheme(savedTheme);
}

// Set theme mode
function setTheme(mode) {
    currentTheme = mode;
    localStorage.setItem(THEME_KEY, mode);

    const root = document.documentElement;

    // Remove existing system theme listener
    if (systemThemeListener) {
        window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', systemThemeListener);
        systemThemeListener = null;
    }

    if (mode === 'dark') {
        root.classList.add('dark');
    } else if (mode === 'light') {
        root.classList.remove('dark');
    } else if (mode === 'system') {
        // Check system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }

        // Listen for system theme changes
        systemThemeListener = (e) => {
            if (currentTheme === 'system') {
                if (e.matches) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
            }
        };
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', systemThemeListener);
    }
}

// Theme toggle functions (to be called from profile/edit buttons)
function setLightMode() {
    setTheme('light');
}

function setDarkMode() {
    setTheme('dark');
}

function setSystemMode() {
    setTheme('system');
}

// Get current theme
function getCurrentTheme() {
    return currentTheme;
}

// Get current applied theme (considering system preference)
function getAppliedTheme() {
    if (currentTheme === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return currentTheme;
}

// Check if dark mode is currently applied
function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

// Export theme functions to window for global access
window.ChatTheme = {
    setLightMode,
    setDarkMode,
    setSystemMode,
    getCurrentTheme,
    getAppliedTheme,
    isDarkMode,
    initializeTheme
};

// ============================================================
// BOOT — runs when page loads
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // Initialize theme
    initializeTheme();

    // If a conversation is active load messages and subscribe
    if (ACTIVE_CONV_ID) {
        loadMessages(ACTIVE_CONV_ID);
        subscribeToConversation(ACTIVE_CONV_ID);
        markAsRead(ACTIVE_CONV_ID);
    }

    // Subscribe to personal channel for new conversation notifications
    subscribeToPersonalChannel();

    // Auto resize textarea as user types
    const input = document.getElementById('message-input');
    if (input) {
        input.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px'; // 120px = ~5 lines
        });
    }
});

// ============================================================
// ECHO — Subscribe to conversation channel
// ============================================================

function subscribeToConversation(conversationId) {
    if (!window.Echo) {
        console.warn('Echo not initialized. Is Reverb running?');
        return;
    }

    // Leave previous channel if switching conversations
    if (echoChannel) {
        window.Echo.leave(echoChannel);
    }

    echoChannel = `conversation.${conversationId}`;

    window.Echo
        .private(echoChannel)

        // ── New message received ──────────────────────────────
        .listen('.message.sent', (e) => {
            appendMessage(e.message, false);
            scrollToBottom();
            markAsRead(conversationId);
            updateSidebarLastMessage(e.conversation_id, e.message);
            updateChatUnreadBadge(e.conversation_id, 0);
        })

        // ── Typing indicator ──────────────────────────────────
        .listen('.user.typing', (e) => {
            if (e.user.id === CURRENT_USER_ID) return;
            showTypingIndicator(e.user.name, e.is_typing);
        });
}

// ============================================================
// ECHO — Subscribe to personal channel
// ============================================================

function subscribeToPersonalChannel() {
    if (!window.Echo) return;

    window.Echo
        .private(`user.${CURRENT_USER_ID}`)
        .listen('.conversation.created', (e) => {
            // New conversation appeared — prepend to sidebar
            prependConversationToSidebar(e.conversation);
            showChatToast(
                `New conversation: ${e.conversation.name || 'Direct message'}`
            );
        });
}

// ============================================================
// MESSAGES — Load messages via fetch
// ============================================================

function renderMessageSkeletons() {
    const container = document.getElementById('messages-container');
    if (!container) return;

    container.innerHTML = `
        <div class="space-y-3 px-1">
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
        </div>`;
}

async function loadMessages(conversationId, beforeId = null) {
    try {
        if (!beforeId) {
            renderMessageSkeletons();
        }

        let url = `/chat/conversations/${conversationId}/messages`;
        if (beforeId) url += `?before_id=${beforeId}`;

        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
            },
        });

        const data = await res.json();

        if (!data.success) return;

        const container = document.getElementById('messages-container');

        if (!beforeId) {
            // First load — clear spinner and render messages
            container.innerHTML = '';

            if (data.data.length === 0) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center
                                py-16 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                            No messages yet. Say hello! 👋
                        </p>
                    </div>`;
                return;
            }
            console.log('Loaded messages:', data.data);
            data.data.forEach(msg => appendMessage(msg, true));
            scrollToBottom(smooth = false);

        } else {
            // Infinite scroll — prepend older messages
            const scrollHeightBefore = document.getElementById('message-thread').scrollHeight;

            data.data.forEach(msg => prependMessage(msg));

            // Maintain scroll position after prepend
            const thread = document.getElementById('message-thread');
            thread.scrollTop = thread.scrollHeight - scrollHeightBefore;
        }

        // Show/hide load more button
        hasMoreMessages = data.has_more;
        oldestMessageId = data.next_before_id;
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.classList.toggle('hidden', !hasMoreMessages);
        }

    } catch (err) {
        console.error('Load messages error:', err);
    }
}

// ── Load older messages when user scrolls up ─────────────────
function loadMoreMessages() {
    if (!hasMoreMessages || !oldestMessageId || !ACTIVE_CONV_ID) return;
    loadMessages(ACTIVE_CONV_ID, oldestMessageId);
}

// ============================================================
// MESSAGES — Render a single message bubble
// ============================================================

function buildMessageHTML(msg, isOwnMessage, showAvatar = true, showName = true) {
    if (msg.is_deleted) {
        return `
        <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-1 ">
            <span class="text-xs text-white dark:text-white  px-3 py-1.5
                       bg-gray-100 dark:bg-gray-800/60 rounded-2xl">
                🚫 This message was deleted
            </span>
        </div>`;
    }

    const time = msg.time || '';
    const edited = msg.is_edited
        ? '<span class="text-[10px] text-slate-400 dark:text-slate-500 ml-1">(edited)</span>'
        : '';

    // Reply preview
    let replyHtml = '';
    if (msg.reply_to) {
        replyHtml = `
        <div class="border-l-2 border-blue-400 pl-2 mb-1.5
                    bg-blue-50/60 dark:bg-blue-900/20 rounded-xl py-1 pr-2">
            <p class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 truncate">
                ${escapeHtmlChat(msg.reply_to.sender)}
            </p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                ${escapeHtmlChat(msg.reply_to.body || 'Attachment')}
            </p>
        </div>`;
    }

    // Attachment / body HTML
    let bodyHtml = '';
    if (msg.type === 'image' && msg.attachment_url) {
        bodyHtml = `
        <img src="${msg.attachment_url}"
             alt="${escapeHtmlChat(msg.attachment_name || 'Image')}"
             class="max-w-xs max-h-56 rounded-2xl object-cover cursor-pointer
                    hover:opacity-90 transition-opacity mt-1 block"
             onclick="openImageLightbox('${msg.attachment_url}')">`;
    } else if (msg.type === 'file' && msg.attachment_url) {
        bodyHtml = `
        <a href="${msg.attachment_url}" target="_blank"
           class="inline-flex items-center gap-2 bg-black/10 dark:bg-white/10
                  rounded-xl px-3 py-2 text-sm hover:bg-black/20 dark:hover:bg-white/20
                  transition-colors mt-1">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586
                         a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            <span class="truncate max-w-[160px] font-medium text-sm">
                ${escapeHtmlChat(msg.attachment_name || 'File')}
            </span>
            <span class="text-[11px] opacity-70 flex-shrink-0">${msg.formatted_size || ''}</span>
        </a>`;
    } else if (msg.type === 'system') {
        return `
        <div class="flex justify-center my-4" id="msg-${msg.id}">
            <span class="text-[11px] text-slate-400 dark:text-slate-500
                         bg-slate-100 dark:bg-slate-800 px-4 py-1.5 rounded-full">
                ${escapeHtmlChat(msg.body)}
            </span>
        </div>`;
    } else {
        bodyHtml = `<p class="text-sm leading-relaxed break-words whitespace-pre-wrap text-left">${escapeHtmlChat(msg.body)}</p>`;
    }

    // ── OWN MESSAGE ──────────────────────────────────────────
    if (isOwnMessage) {
        const mb = showAvatar ? 'mb-0.5' : 'mb-0.5';
        return `
        <div class="flex justify-end items-end gap-2 ${mb} group" id="msg-${msg.id}">

            <!-- Hover actions -->
            <div class="hidden group-hover:flex items-center gap-1 self-end pb-1">
                <button onclick="setReply(${msg.id}, '${escapeHtmlChat(CURRENT_USER_NAME)}', '${escapeHtmlChat((msg.body || 'Attachment').substring(0, 50))}')"
                        class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700
                               flex items-center justify-center text-slate-400
                               hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30
                               transition-all"
                        title="Reply">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </button>
                <button onclick="deleteMessage(${msg.id})"
                        class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700
                               flex items-center justify-center text-slate-400
                               hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30
                               transition-all"
                        title="Delete">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>

            <!-- Bubble -->
            <div class="max-w-[55%] sm:max-w-[45%] w-fit">
                ${replyHtml}
                <div class="bg-blue-600 text-white px-2.5 py-1.5 rounded-2xl rounded-br-md text-left
                            text-sm leading-relaxed break-words">
                    ${bodyHtml}
                </div>
                ${showAvatar ? `
                <div class="flex items-center justify-end gap-1 mt-0.5 pr-1">
                    <span class="text-[10px] text-slate-400 dark:text-slate-500">${time}</span>
                    ${edited}
                    <!-- Read receipt ticks -->
                    <svg class="w-3.5 h-3.5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 7l-8 8-4-4-1.4 1.4L10 17.8 19.4 8.4z"/>
                    </svg>
                </div>` : `<div class="flex items-center justify-end gap-1 mt-0.5 pr-1">
                    <span class="text-[10px] text-slate-400 dark:text-slate-500">${time}</span>
                    ${edited}
                </div>`}
            </div>

            <!-- Own avatar (only on last in group) -->
            ${showAvatar ? `
            <div class="w-7 h-7 rounded-full bg-blue-600 flex-shrink-0
                        flex items-center justify-center text-white text-[11px] font-bold self-end">
                ${(CURRENT_USER_NAME || 'Y').charAt(0).toUpperCase()}
            </div>` : `<div class="w-7 flex-shrink-0"></div>`}
        </div>`;
    }

    // ── OTHER USER MESSAGE ──────────────────────────────────
    const initial = (msg.sender?.name || '?').charAt(0).toUpperCase();
    return `
    <div class="flex items-end gap-2 mb-0.5 group" id="msg-${msg.id}">

        <!-- Other avatar (only on last in group) -->
        ${showAvatar ? `
        <div class="w-7 h-7 rounded-full bg-slate-500 dark:bg-slate-600 flex-shrink-0
                    flex items-center justify-center text-white text-[11px] font-bold self-end"
             title="${escapeHtmlChat(msg.sender?.name || '')}">
            ${initial}
        </div>` : `<div class="w-7 flex-shrink-0"></div>`}

        <!-- Bubble -->
        <div class="max-w-[55%] sm:max-w-[45%] w-fit">
            ${showName ? `
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-0.5 ml-1">
                ${escapeHtmlChat(msg.sender?.name || '')}
            </p>` : ''}
            ${replyHtml}
            <div class="bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 text-left
                        px-2.5 py-1.5 rounded-2xl rounded-bl-md
                        border border-slate-200 dark:border-slate-600/50
                        text-sm leading-relaxed break-words">
                ${bodyHtml}
            </div>
            ${showAvatar ? `
            <div class="flex items-center gap-1 mt-0.5 ml-1">
                <span class="text-[10px] text-slate-400 dark:text-slate-500">${time}</span>
                ${edited}
            </div>` : ''}
        </div>

        <!-- Hover actions -->
        <div class="hidden group-hover:flex items-center gap-1 self-end pb-1">
            <button onclick="setReply(${msg.id}, '${escapeHtmlChat(msg.sender?.name || '')}', '${escapeHtmlChat((msg.body || 'Attachment').substring(0, 50))}')"
                    class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700
                           flex items-center justify-center text-slate-400
                           hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30
                           transition-all"
                    title="Reply">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            </button>
        </div>
    </div>`;
}
// ── Append message at the bottom ─────────────────────────────
function appendMessage(msg, isHistoryLoad = false) {
    const container = document.getElementById('messages-container');
    if (!container) return;

    const empty = container.querySelector('.italic');
    if (empty) empty.closest('div')?.remove();

    const isOwn = msg.sender?.id === CURRENT_USER_ID;

    // Check if previous message is from same sender (for grouping)
    const allMsgs = container.querySelectorAll('[id^="msg-"]');
    const lastMsgEl = allMsgs[allMsgs.length - 1];
    const lastSenderId = lastMsgEl?.dataset.senderId;
    const isSameSender = lastSenderId && String(lastSenderId) === String(msg.sender?.id);

    // Check if we need a date separator
    const msgDate = msg.created_at ? new Date(msg.created_at).toDateString() : null;
    const lastDate = container.dataset.lastDate || null;

    if (msgDate && msgDate !== lastDate) {
        container.dataset.lastDate = msgDate;
        const label = getDateLabel(msg.created_at);
        container.insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-3 my-4">
                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium px-2">${label}</span>
                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
            </div>`);
    }

    // Show avatar/name only if last in a group or different sender
    const showAvatar = true; // always show for simplicity; set false for grouping
    const showName = !isSameSender;
    const html = buildMessageHTML(msg, isOwn, showAvatar, showName);

    // Add spacing between groups (different senders)
    const wrapper = document.createElement('div');
    wrapper.dataset.senderId = msg.sender?.id || '';
    if (!isSameSender && allMsgs.length > 0) {
        wrapper.style.marginTop = '10px';
    }
    wrapper.innerHTML = html;
    container.appendChild(wrapper);

    if (!isHistoryLoad) scrollToBottom();
}

function getDateLabel(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);

    if (d.toDateString() === today.toDateString()) return 'Today';
    if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return d.toLocaleDateString(undefined, { weekday: 'long', month: 'short', day: 'numeric' });
}
// ── Prepend message at the top (infinite scroll) ─────────────
function prependMessage(msg) {
    const container = document.getElementById('messages-container');
    if (!container) return;

    const isOwn = msg.sender?.id === CURRENT_USER_ID;
    const html = buildMessageHTML(msg, isOwn);
    container.insertAdjacentHTML('afterbegin', html);
}

// ============================================================
// SEND MESSAGE
// ============================================================

async function sendMessage(conversationId) {
    const input = document.getElementById('message-input');
    const body = input?.value.trim();

    if (!body) return;

    // Clear input immediately
    input.value = '';
    input.style.height = 'auto';

    // Stop typing indicator
    broadcastTyping(conversationId, false);

    try {
        const payload = { body };
        if (replyToId) payload.reply_to_id = replyToId;

        const socketId = window.Echo?.socketId?.() || null;

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': chatCsrf,
        };

        if (socketId) {
            headers['X-Socket-ID'] = socketId;
        }

        const res = await fetch(
            `/chat/conversations/${conversationId}/messages`,
            {
                method: 'POST',
                headers,
                body: JSON.stringify(payload),
            }
        );

        const data = await res.json();

        if (data.success) {
            appendMessage(data.data, false);
            scrollToBottom();
            cancelReply();
            updateSidebarLastMessage(conversationId, data.data);
        }

    } catch (err) {
        console.error('Send message error:', err);
        showChatToast('Failed to send message.', 'error');
        if (input) input.value = body;
    }
}

function handleMessageKeydown(event, conversationId) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage(conversationId);
    }
}


async function sendAttachment(conversationId, input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 20 * 1024 * 1024) {
        showChatToast('File too large. Maximum 20MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', chatCsrf);
    if (replyToId) formData.append('reply_to_id', replyToId);

    showChatToast('Uploading...');

    // Get socket ID to prevent echo back to sender
    const socketId = window.Echo?.socketId?.() || null;

    const headers = { 'Accept': 'application/json' };
    if (socketId) {
        headers['X-Socket-ID'] = socketId;
    }

    try {
        const res = await fetch(
            `/chat/conversations/${conversationId}/messages`,
            {
                method: 'POST',
                body: formData,
                headers,
            }
        );

        const data = await res.json();

        if (data.success) {
            appendMessage(data.data, false);
            scrollToBottom();
            cancelReply();
            updateSidebarLastMessage(conversationId, data.data);
            showChatToast('File sent.');
        }
    } catch {
        showChatToast('Upload failed.', 'error');
    }

    input.value = '';
}

// ============================================================
// DELETE MESSAGE
// ============================================================

async function deleteMessage(messageId) {
    if (!confirm('Delete this message?')) return;

    try {
        const res = await fetch(`/chat/messages/${messageId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
            },
        });

        const data = await res.json();

        if (data.success) {
            // Replace message bubble with "deleted" state
            const el = document.getElementById(`msg-${messageId}`);
            if (el) {
                el.outerHTML = `
                <div class="flex justify-end mb-1" id="msg-${messageId}">
                    <span class="text-xs text-white dark:text-white  px-3 py-1.5 bg-gray-100 dark:bg-gray-800/60 rounded-2xl">
                        🚫 This message was deleted
                    </span>
                </div>`;
            }
        }
    } catch {
        showChatToast('Failed to delete message.', 'error');
    }
}

// ============================================================
// REPLY
// ============================================================

function setReply(messageId, senderName, bodyPreview) {
    replyToId = messageId;

    const bar = document.getElementById('reply-bar');
    const name = document.getElementById('reply-to-name');
    const body = document.getElementById('reply-to-body');

    if (bar) bar.classList.remove('hidden');
    if (name) name.textContent = senderName;
    if (body) body.textContent = bodyPreview;

    document.getElementById('message-input')?.focus();
}

function cancelReply() {
    replyToId = null;
    document.getElementById('reply-bar')?.classList.add('hidden');
    document.getElementById('reply-to-name').textContent = '';
    document.getElementById('reply-to-body').textContent = '';
}

// ============================================================
// TYPING INDICATOR
// ============================================================

function handleTypingInput(conversationId) {
    if (!isTyping) {
        isTyping = true;
        broadcastTyping(conversationId, true);
    }

    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        isTyping = false;
        broadcastTyping(conversationId, false);
    }, 2000);
    // Stop typing indicator after 2 seconds of no input
}

async function broadcastTyping(conversationId, typing) {
    try {
        await fetch(`/chat/conversations/${conversationId}/typing`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
            },
            body: JSON.stringify({ is_typing: typing }),
        });
    } catch {
        // Typing is non-critical — silently ignore errors
    }
}

function showTypingIndicator(userName, isTyping) {
    const indicator = document.getElementById('typing-indicator');
    const text = document.getElementById('typing-text');

    if (!indicator) return;

    if (isTyping) {
        if (text) text.textContent = `${userName} is typing...`;
        indicator.classList.remove('hidden');
        scrollToBottom();
    } else {
        indicator.classList.add('hidden');
    }
}

// ============================================================
// MARK AS READ
// ============================================================

async function markAsRead(conversationId) {
    try {
        await fetch(`/chat/conversations/${conversationId}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
            },
        });

        // Clear unread badge in sidebar
        updateChatUnreadBadge(conversationId, 0);

    } catch {
        // Non critical — ignore
    }
}

// ============================================================
// MUTE CONVERSATION
// ============================================================

async function toggleMute(conversationId) {
    try {
        const res = await fetch(
            `/chat/conversations/${conversationId}/mute`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': chatCsrf,
                },
            }
        );

        const data = await res.json();
        if (data.success) {
            showChatToast(data.message);
        }
    } catch {
        showChatToast('Failed to update mute.', 'error');
    }
}

// ============================================================
// NEW DIRECT MESSAGE MODAL
// ============================================================

function openNewDirectModal() {
    document.getElementById('new-direct-modal')?.classList.remove('hidden');
    document.getElementById('user-search-input')?.focus();
}

function closeNewDirectModal() {
    document.getElementById('new-direct-modal')?.classList.add('hidden');
    document.getElementById('user-search-input').value = '';
    document.getElementById('user-search-results').innerHTML = `
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6 italic">
            Start typing to search users
        </p>`;
}

let userSearchTimer = null;

function searchUsers(query) {
    clearTimeout(userSearchTimer);

    if (!query.trim()) {
        document.getElementById('user-search-results').innerHTML = `
            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6 italic">
                Start typing to search users
            </p>`;
        return;
    }

    userSearchTimer = setTimeout(async () => {
        try {
            const res = await fetch(
                `/chat/users/search?query=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': chatCsrf,
                    },
                }
            );

            const data = await res.json();
            const container = document.getElementById('user-search-results');

            if (!data.data?.length) {
                container.innerHTML = `
                    <p class="text-sm text-gray-400 text-center py-4 italic">
                        No users found
                    </p>`;
                return;
            }

            container.innerHTML = data.data.map(u => `
                <button onclick="startDirectChat(${u.id})"
                        class="w-full flex items-center gap-4 px-4 py-3.5
                               rounded-2xl hover:bg-slate-50/80 dark:hover:bg-slate-700/50
                               transition-all duration-200 text-left group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center
                                justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            ${escapeHtmlChat(u.name)}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            ${escapeHtmlChat(u.email)}
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>`
            ).join('');

        } catch {
            showChatToast('Search failed.', 'error');
        }
    }, 300);
}

async function startDirectChat(userId) {
    try {
        const res = await fetch('/chat/conversations/direct', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
            },
            body: JSON.stringify({ user_id: userId }),
        });

        const data = await res.json();

        if (data.success) {
            closeNewDirectModal();
            window.location.href = `/chat/conversations/${data.data.id}`;
        }
    } catch {
        showChatToast('Failed to start conversation.', 'error');
    }
}

// ============================================================
// NEW GROUP MODAL
// ============================================================

function openNewGroupModal() {
    document.getElementById('new-group-modal')?.classList.remove('hidden');
    document.getElementById('group-name-input')?.focus();
}

function closeNewGroupModal() {
    document.getElementById('new-group-modal')?.classList.add('hidden');
    document.getElementById('group-name-input').value = '';
    document.getElementById('group-user-search').value = '';
    document.getElementById('group-user-results').innerHTML = '';
    document.getElementById('selected-members').innerHTML = '';
    Object.keys(groupSelectedUsers).forEach(k => delete groupSelectedUsers[k]);
}

let groupSearchTimer = null;

function searchGroupUsers(query) {
    clearTimeout(groupSearchTimer);
    if (!query.trim()) {
        document.getElementById('group-user-results').innerHTML = '';
        return;
    }

    groupSearchTimer = setTimeout(async () => {
        try {
            const res = await fetch(
                `/chat/users/search?query=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': chatCsrf,
                    },
                }
            );

            const data = await res.json();
            const container = document.getElementById('group-user-results');

            if (!data.data?.length) {
                container.innerHTML = `
                    <p class="text-xs text-gray-400 text-center py-3 italic">
                        No users found
                    </p>`;
                return;
            }

            container.innerHTML = data.data.map(u => `
                <button onclick="toggleGroupMember(${u.id}, '${escapeHtmlChat(u.name)}')"
                        id="group-user-btn-${u.id}"
                        class="w-full flex items-center gap-4 px-4 py-3
                               rounded-2xl hover:bg-slate-50/80 dark:hover:bg-slate-700/50
                               transition-all duration-200 text-left group
                               ${groupSelectedUsers[u.id] ? 'bg-blue-50/50 dark:bg-blue-900/30 border border-blue-200/50 dark:border-blue-800/50' : ''}">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center
                                justify-center text-white font-bold text-sm flex-shrink-0 shadow-md">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="text-sm text-slate-900 dark:text-white flex-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        ${escapeHtmlChat(u.name)}
                    </span>
                    ${groupSelectedUsers[u.id]
                    ? '<div class="w-6 h-6 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-md"><svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>'
                    : '<div class="w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-full group-hover:border-blue-400 transition-colors"></div>'}
                </button>`
            ).join('');

        } catch {
            showChatToast('Search failed.', 'error');
        }
    }, 300);
}

function toggleGroupMember(userId, userName) {
    if (groupSelectedUsers[userId]) {
        delete groupSelectedUsers[userId];
    } else {
        groupSelectedUsers[userId] = userName;
    }

    // Refresh selected members chips
    const chips = document.getElementById('selected-members');
    chips.innerHTML = Object.entries(groupSelectedUsers).map(([id, name]) => `
        <span class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40
                     text-blue-700 dark:text-blue-300 text-sm font-medium
                     px-3 py-2 rounded-2xl shadow-sm border border-blue-200/50 dark:border-blue-800/50">
            <div class="w-5 h-5 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                ${name.charAt(0).toUpperCase()}
            </div>
            ${escapeHtmlChat(name)}
            <button onclick="toggleGroupMember(${id}, '${escapeHtmlChat(name)}')"
                    class="ml-1 hover:text-blue-900 dark:hover:text-blue-100 text-lg hover:scale-110 transition-transform">
                &times;
            </button>
        </span>`
    ).join('');

    // Re-render search results to show updated state
    searchGroupUsers(document.getElementById('group-user-search').value);
}

async function createGroup() {
    const name = document.getElementById('group-name-input').value.trim();
    const userIds = Object.keys(groupSelectedUsers).map(Number);

    if (!name) {
        showChatToast('Please enter a group name.', 'error');
        return;
    }

    if (userIds.length === 0) {
        showChatToast('Please add at least one member.', 'error');
        return;
    }

    try {
        const res = await fetch('/chat/conversations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': chatCsrf,
            },
            body: JSON.stringify({ name, user_ids: userIds }),
        });

        const data = await res.json();

        if (data.success) {
            closeNewGroupModal();
            window.location.href = `/chat/conversations/${data.data.id}`;
        }
    } catch {
        showChatToast('Failed to create group.', 'error');
    }
}

// ============================================================
// ADD MEMBER MODAL (group conversations)
// ============================================================

function openAddMemberModal(conversationId) {
    document.getElementById('add-member-conv-id').value = conversationId;
    document.getElementById('add-member-modal')?.classList.remove('hidden');
    document.getElementById('add-member-search')?.focus();
}

function closeAddMemberModal() {
    document.getElementById('add-member-modal')?.classList.add('hidden');
    document.getElementById('add-member-search').value = '';
    document.getElementById('add-member-results').innerHTML = '';
}

let addMemberTimer = null;

function searchAddMember(query) {
    clearTimeout(addMemberTimer);
    if (!query.trim()) {
        document.getElementById('add-member-results').innerHTML = '';
        return;
    }

    addMemberTimer = setTimeout(async () => {
        try {
            const res = await fetch(
                `/chat/users/search?query=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': chatCsrf,
                    },
                }
            );

            const data = await res.json();
            const container = document.getElementById('add-member-results');
            const convId = document.getElementById('add-member-conv-id').value;

            if (!data.data?.length) {
                container.innerHTML = `
                    <p class="text-xs text-gray-400 text-center py-3 italic">
                        No users found
                    </p>`;
                return;
            }

            container.innerHTML = data.data.map(u => `
                <button onclick="addMemberToConversation(${convId}, ${u.id}, '${escapeHtmlChat(u.name)}')"
                        class="w-full flex items-center gap-4 px-4 py-3.5
                               rounded-2xl hover:bg-slate-50/80 dark:hover:bg-slate-700/50
                               transition-all duration-200 text-left group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center
                                justify-center text-white font-bold text-sm flex-shrink-0 shadow-md">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            ${escapeHtmlChat(u.name)}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">${escapeHtmlChat(u.email)}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition-colors ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>`
            ).join('');

        } catch {
            showChatToast('Search failed.', 'error');
        }
    }, 300);
}

async function addMemberToConversation(conversationId, userId, userName) {
    try {
        const res = await fetch(
            `/chat/conversations/${conversationId}/members`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': chatCsrf,
                },
                body: JSON.stringify({ user_id: userId }),
            }
        );

        const data = await res.json();

        if (data.success) {
            closeAddMemberModal();
            showChatToast(`${userName} added to the conversation.`);
        } else {
            showChatToast(data.message, 'error');
        }
    } catch {
        showChatToast('Failed to add member.', 'error');
    }
}

// ============================================================
// SIDEBAR HELPERS
// ============================================================

// Filter conversations by search input
function filterConversations(query) {
    const items = document.querySelectorAll('.conv-item');
    const q = query.trim().toLowerCase();

    items.forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = name.includes(q) ? '' : 'none';
    });
}

// Update last message preview in sidebar after sending
function updateSidebarLastMessage(conversationId, message) {
    const item = document.getElementById(`conv-item-${conversationId}`);
    if (!item) return;

    const preview = item.querySelector('p.text-xs.text-gray-500');
    if (preview) {
        const body = message.body
            ? message.body.substring(0, 40)
            : 'Sent an attachment';
        preview.textContent = body;
    }

    // Move this conversation to the top of the sidebar
    const list = document.getElementById('conversation-list');
    if (list) list.prepend(item);
}

// Update unread badge in sidebar
function updateChatUnreadBadge(conversationId, count) {
    const badge = document.getElementById(`unread-${conversationId}`);
    if (!badge) return;

    if (count <= 0) {
        badge.classList.add('hidden');
        badge.textContent = '';
    } else {
        badge.classList.remove('hidden');
        badge.textContent = count > 9 ? '9+' : count;
    }

    // Also update navbar chat badge
    updateNavChatBadge();
}

// Recalculate total unread for navbar badge
function updateNavChatBadge() {
    const badges = document.querySelectorAll('[id^="unread-"]');
    let total = 0;

    badges.forEach(b => {
        if (!b.classList.contains('hidden')) {
            total += parseInt(b.textContent) || 0;
        }
    });

    const navBadge = document.getElementById('chat-unread-badge');
    if (!navBadge) return;

    if (total <= 0) {
        navBadge.classList.add('hidden');
        navBadge.textContent = '';
    } else {
        navBadge.classList.remove('hidden');
        navBadge.textContent = total > 9 ? '9+' : total;
    }
}

// Prepend new conversation to sidebar
function prependConversationToSidebar(conv) {
    const list = document.getElementById('conversation-list');
    if (!list) return;

    const initial = (conv.name || 'D').charAt(0).toUpperCase();
    const html = `
    <a href="/chat/conversations/${conv.id}"
       id="conv-item-${conv.id}"
       class="conv-item flex items-center gap-3 py-2.5 cursor-pointer
              my-0.5 rounded-xl transition-all duration-200
              hover:bg-slate-100/80 dark:hover:bg-slate-800/50
              border border-transparent hover:border-slate-200/50 dark:hover:border-slate-700/50"
       data-name="${escapeHtmlChat((conv.name || '').toLowerCase())}">
        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center
                    justify-center text-white font-bold text-xs flex-shrink-0">
            ${initial}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-900
                             dark:text-white truncate max-w-[140px]">
                    ${escapeHtmlChat(conv.name || 'Direct message')}
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 italic mt-0.5">
                No messages yet
            </p>
        </div>
    </a>`;

    list.insertAdjacentHTML('afterbegin', html);
}

// ============================================================
// SCROLL HELPERS
// ============================================================

function scrollToBottom(smooth = true) {
    const thread = document.getElementById('message-thread');
    if (!thread) return;

    thread.scrollTo({
        top: thread.scrollHeight,
        behavior: smooth ? 'smooth' : 'auto',
    });
}

// ============================================================
// IMAGE LIGHTBOX
// ============================================================

function openImageLightbox(url) {
    const existing = document.getElementById('chat-lightbox');
    if (existing) existing.remove();

    const lightbox = document.createElement('div');
    lightbox.id = 'chat-lightbox';
    lightbox.className = 'fixed inset-0 z-[9999] flex items-center justify-center px-4';
    lightbox.style.backgroundColor = 'rgba(0,0,0,0.85)';
    lightbox.style.backdropFilter = 'blur(12px)';
    lightbox.innerHTML = `
        <div class="relative max-w-5xl w-full">
            <button onclick="document.getElementById('chat-lightbox').remove()"
                    class="absolute -top-12 right-0 text-white/80 hover:text-white text-3xl font-bold
                           hover:scale-110 transition-all duration-200 shadow-lg bg-black/20 rounded-full w-10 h-10 flex items-center justify-center backdrop-blur-sm">
                &times;
            </button>
            <img src="${url}" alt="Full size"
                 class="w-full max-h-[85vh] object-contain rounded-3xl shadow-2xl border border-white/10">
        </div>`;

    lightbox.addEventListener('click', function (e) {
        if (e.target === this) this.remove();
    });

    document.body.appendChild(lightbox);
}

// ============================================================
// TOAST NOTIFICATION
// ============================================================

function showChatToast(message, type = 'success') {
    document.querySelectorAll('.chat-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'chat-toast fixed bottom-6 right-6 z-[9999] px-6 py-4 '
        + 'rounded-2xl text-white text-sm font-semibold shadow-2xl backdrop-blur-xl '
        + 'transition-all duration-300 border border-white/20 '
        + (type === 'error' ? 'bg-gradient-to-r from-red-500 to-pink-500' : 'bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-700 dark:to-slate-600');
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px) scale(0.95)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// UTILITY
// ============================================================

function escapeHtmlChat(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
}

// ============================================================
// MOBILE SIDEBAR FUNCTIONALITY
// ============================================================

function toggleMobileSidebar() {
    const sidebar = document.getElementById('conversation-sidebar');
    const backdrop = document.getElementById('mobile-sidebar-backdrop');
    const isOpen = !sidebar.classList.contains('-translate-x-full');

    if (isOpen) {
        closeMobileSidebar();
    } else {
        openMobileSidebar();
    }
}

function openMobileSidebar() {
    const sidebar = document.getElementById('conversation-sidebar');
    const backdrop = document.getElementById('mobile-sidebar-backdrop');

    sidebar.classList.remove('-translate-x-full');
    backdrop.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('conversation-sidebar');
    const backdrop = document.getElementById('mobile-sidebar-backdrop');

    sidebar.classList.add('-translate-x-full');
    backdrop.classList.add('hidden');
    document.body.style.overflow = '';
}

// Initialize mobile functionality
document.addEventListener('DOMContentLoaded', function () {
    // Add click handlers to conversation items for mobile
    document.addEventListener('click', function (e) {
        const convItem = e.target.closest('.conv-item');
        if (convItem && window.innerWidth < 1024) {
            // Small delay to allow navigation to happen
            setTimeout(closeMobileSidebar, 100);
        }
    });

    // Close sidebar on window resize if now on desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            closeMobileSidebar();
        }
    });
});