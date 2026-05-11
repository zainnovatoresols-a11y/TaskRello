// ============================================================
// CHAT.JS — Real-time chat with Laravel Echo + Reverb
// ============================================================

const chatCsrf        = document.querySelector('meta[name="csrf-token"]').content;
let   replyToId       = null;
let   typingTimer     = null;
let   isTyping        = false;
let   oldestMessageId = null;
let   hasMoreMessages = true;
let   echoChannel     = null;
const groupSelectedUsers = {};

// ============================================================
// BOOT — runs when page loads
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

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
            this.style.height = Math.min(this.scrollHeight, 128) + 'px';
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

async function loadMessages(conversationId, beforeId = null) {
    try {
        let url = `/chat/conversations/${conversationId}/messages`;
        if (beforeId) url += `?before_id=${beforeId}`;

        const res  = await fetch(url, {
            headers: {
                'Accept':       'application/json',
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
        hasMoreMessages       = data.has_more;
        oldestMessageId       = data.next_before_id;
        const loadMoreBtn     = document.getElementById('load-more-btn');
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

function buildMessageHTML(msg, isOwnMessage) {
    if (msg.is_deleted) {
        return `
        <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'}
                    mb-1" id="msg-${msg.id}">
            <p class="text-xs text-gray-400 dark:text-gray-600 italic px-3 py-1.5
                       bg-gray-100 dark:bg-gray-800 rounded-xl">
                This message was deleted
            </p>
        </div>`;
    }

    const time    = msg.time || '';
    const edited  = msg.is_edited
        ? '<span class="text-xs text-gray-400 dark:text-gray-500 ml-1">(edited)</span>'
        : '';

    // Reply preview
    let replyHtml = '';
    if (msg.reply_to) {
        replyHtml = `
        <div class="border-l-2 border-blue-400 pl-2 mb-1.5
                    bg-blue-50 dark:bg-blue-900/20 rounded py-1 pr-2">
            <p class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                ${escapeHtmlChat(msg.reply_to.sender)}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                ${escapeHtmlChat(msg.reply_to.body || 'Attachment')}
            </p>
        </div>`;
    }

    // Attachment content
    let bodyHtml = '';
    if (msg.type === 'image' && msg.attachment_url) {
        bodyHtml = `
        <img src="${msg.attachment_url}"
             alt="${escapeHtmlChat(msg.attachment_name || 'Image')}"
             class="max-w-xs max-h-60 rounded-xl object-cover cursor-pointer
                    hover:opacity-90 transition mt-1"
             onclick="openImageLightbox('${msg.attachment_url}')">`;
    } else if (msg.type === 'file' && msg.attachment_url) {
        bodyHtml = `
        <a href="${msg.attachment_url}" target="_blank"
           class="inline-flex items-center gap-2 bg-white/20 dark:bg-black/20
                  rounded-xl px-3 py-2 text-sm hover:bg-white/30 transition mt-1">
            <svg class="w-4 h-4 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586
                         a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486
                         8.486L20.5 13"/>
            </svg>
            <span class="truncate max-w-[150px]">
                ${escapeHtmlChat(msg.attachment_name || 'File')}
            </span>
            <span class="text-xs opacity-70 flex-shrink-0">
                ${msg.formatted_size || ''}
            </span>
        </a>`;
    } else if (msg.type === 'system') {
        return `
        <div class="flex justify-center my-2" id="msg-${msg.id}">
            <span class="text-xs text-gray-400 dark:text-gray-500
                         bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                ${escapeHtmlChat(msg.body)}
            </span>
        </div>`;
    } else {
        bodyHtml = `
        <p class="text-sm leading-relaxed break-words whitespace-pre-wrap">
            ${escapeHtmlChat(msg.body || '')}
        </p>`;
    }

    if (isOwnMessage) {
        return `
        <div class="flex justify-end mb-1 group" id="msg-${msg.id}">
            <div class="max-w-xs lg:max-w-md">
                ${replyHtml}
                <div class="relative">
                    <div class="bg-blue-700 text-white px-4 py-2.5 rounded-2xl
                                rounded-br-sm shadow-sm">
                        ${bodyHtml}
                    </div>
                    <div class="absolute -left-20 top-1/2 -translate-y-1/2
                                hidden group-hover:flex items-center gap-1">
                        <button onclick="setReply(${msg.id}, '${escapeHtmlChat(CURRENT_USER_NAME)}', '${escapeHtmlChat((msg.body || 'Attachment').substring(0, 50))}')"
                                class="w-6 h-6 bg-white dark:bg-gray-700 rounded-full
                                       shadow flex items-center justify-center
                                       text-gray-400 hover:text-blue-600 transition"
                                title="Reply">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </button>
                        <button onclick="deleteMessage(${msg.id})"
                                class="w-6 h-6 bg-white dark:bg-gray-700 rounded-full
                                       shadow flex items-center justify-center
                                       text-gray-400 hover:text-red-500 transition"
                                title="Delete">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-1 mt-0.5 pr-1">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        ${time}
                    </span>
                    ${edited}
                </div>
            </div>
        </div>`;
    } else {
        const initial = (msg.sender?.name || '?').charAt(0).toUpperCase();
        return `
        <div class="flex items-end gap-2 mb-1 group" id="msg-${msg.id}">
            <div class="w-7 h-7 rounded-full bg-blue-600 flex-shrink-0
                        flex items-center justify-center text-white
                        text-xs font-bold mb-0.5"
                 title="${escapeHtmlChat(msg.sender?.name || '')}">
                ${initial}
            </div>
            <div class="max-w-xs lg:max-w-md">
                <p class="text-xs text-gray-500 dark:text-gray-400
                           font-medium mb-0.5 ml-1">
                    ${escapeHtmlChat(msg.sender?.name || '')}
                </p>
                ${replyHtml}
                <div class="relative">
                    <div class="bg-white dark:bg-gray-800 text-gray-800
                                dark:text-gray-100 px-4 py-2.5 rounded-2xl
                                rounded-bl-sm shadow-sm border
                                border-gray-100 dark:border-gray-700">
                        ${bodyHtml}
                    </div>
                    <div class="absolute -right-14 top-1/2 -translate-y-1/2
                                hidden group-hover:flex items-center gap-1">
                        <button onclick="setReply(${msg.id}, '${escapeHtmlChat(msg.sender?.name || '')}', '${escapeHtmlChat((msg.body || 'Attachment').substring(0, 50))}')"
                                class="w-6 h-6 bg-white dark:bg-gray-700 rounded-full
                                       shadow flex items-center justify-center
                                       text-gray-400 hover:text-blue-600 transition"
                                title="Reply">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-0.5 ml-1">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        ${time}
                    </span>
                    ${edited}
                </div>
            </div>
        </div>`;
    }
}

// ── Append message at the bottom ─────────────────────────────
function appendMessage(msg, isHistoryLoad = false) {
    const container   = document.getElementById('messages-container');
    if (!container) return;

    // Remove empty state if present
    const empty = container.querySelector('.italic');
    if (empty) empty.closest('div')?.remove();

    const isOwn  = msg.sender?.id === CURRENT_USER_ID;
    const html   = buildMessageHTML(msg, isOwn);
    container.insertAdjacentHTML('beforeend', html);

    if (!isHistoryLoad) scrollToBottom();
}

// ── Prepend message at the top (infinite scroll) ─────────────
function prependMessage(msg) {
    const container = document.getElementById('messages-container');
    if (!container) return;

    const isOwn = msg.sender?.id === CURRENT_USER_ID;
    const html  = buildMessageHTML(msg, isOwn);
    container.insertAdjacentHTML('afterbegin', html);
}

// ============================================================
// SEND MESSAGE
// ============================================================

async function sendMessage(conversationId) {
    const input = document.getElementById('message-input');
    const body  = input?.value.trim();

    if (!body) return;

    // Optimistically clear input
    input.value       = '';
    input.style.height = 'auto';

    // Stop typing indicator
    broadcastTyping(conversationId, false);

    try {
        const payload = { body };
        if (replyToId) payload.reply_to_id = replyToId;

        const res  = await fetch(
            `/chat/conversations/${conversationId}/messages`,
            {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': chatCsrf,
                },
                body: JSON.stringify(payload),
            }
        );

        const data = await res.json();

        if (data.success) {
            // Append own message immediately (Echo only fires toOthers)
            appendMessage(data.data, false);
            scrollToBottom();
            cancelReply();
            updateSidebarLastMessage(conversationId, data.data);
        }
    } catch (err) {
        console.error('Send message error:', err);
        showChatToast('Failed to send message.', 'error');
        // Restore the message in input if failed
        if (input) input.value = body;
    }
}

// ── Handle Enter key in textarea ─────────────────────────────
function handleMessageKeydown(event, conversationId) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage(conversationId);
    }
}

// ============================================================
// SEND ATTACHMENT
// ============================================================

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

    try {
        const res  = await fetch(
            `/chat/conversations/${conversationId}/messages`,
            {
                method:  'POST',
                body:    formData,
                headers: { 'Accept': 'application/json' },
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
    } catch (err) {
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
        const res  = await fetch(`/chat/messages/${messageId}`, {
            method:  'DELETE',
            headers: {
                'Accept':       'application/json',
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
                    <p class="text-xs text-gray-400 dark:text-gray-600 italic
                               px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-xl">
                        This message was deleted
                    </p>
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

    const bar  = document.getElementById('reply-bar');
    const name = document.getElementById('reply-to-name');
    const body = document.getElementById('reply-to-body');

    if (bar)  bar.classList.remove('hidden');
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
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
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
    const text      = document.getElementById('typing-text');

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
            method:  'POST',
            headers: {
                'Accept':       'application/json',
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
        const res  = await fetch(
            `/chat/conversations/${conversationId}/mute`,
            {
                method:  'POST',
                headers: {
                    'Accept':       'application/json',
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
    document.getElementById('user-search-input').value      = '';
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
            const res  = await fetch(
                `/chat/users/search?query=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept':       'application/json',
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
                        class="w-full flex items-center gap-3 px-3 py-2.5
                               rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700
                               transition text-left">
                    <div class="w-9 h-9 rounded-full bg-blue-700 flex items-center
                                justify-center text-white font-bold text-sm flex-shrink-0">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            ${escapeHtmlChat(u.name)}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                            ${escapeHtmlChat(u.email)}
                        </p>
                    </div>
                </button>`
            ).join('');

        } catch {
            showChatToast('Search failed.', 'error');
        }
    }, 300);
}

async function startDirectChat(userId) {
    try {
        const res  = await fetch('/chat/conversations/direct', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
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
    document.getElementById('group-name-input').value        = '';
    document.getElementById('group-user-search').value       = '';
    document.getElementById('group-user-results').innerHTML  = '';
    document.getElementById('selected-members').innerHTML    = '';
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
            const res  = await fetch(
                `/chat/users/search?query=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': chatCsrf,
                    },
                }
            );

            const data      = await res.json();
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
                        class="w-full flex items-center gap-3 px-3 py-2
                               rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700
                               transition text-left
                               ${groupSelectedUsers[u.id] ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                    <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center
                                justify-center text-white font-bold text-xs flex-shrink-0">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="text-sm text-gray-900 dark:text-white flex-1">
                        ${escapeHtmlChat(u.name)}
                    </span>
                    ${groupSelectedUsers[u.id]
                        ? '<span class="text-blue-700 dark:text-blue-400 text-xs font-medium">Added ✓</span>'
                        : ''}
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
        <span class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/40
                     text-blue-700 dark:text-blue-300 text-xs font-medium
                     px-2.5 py-1 rounded-full">
            ${escapeHtmlChat(name)}
            <button onclick="toggleGroupMember(${id}, '${escapeHtmlChat(name)}')"
                    class="ml-0.5 hover:text-blue-900 dark:hover:text-blue-100">
                &times;
            </button>
        </span>`
    ).join('');

    // Re-render search results to show updated state
    searchGroupUsers(document.getElementById('group-user-search').value);
}

async function createGroup() {
    const name    = document.getElementById('group-name-input').value.trim();
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
        const res  = await fetch('/chat/conversations', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
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
    document.getElementById('add-member-search').value      = '';
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
            const res  = await fetch(
                `/chat/users/search?query=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': chatCsrf,
                    },
                }
            );

            const data      = await res.json();
            const container = document.getElementById('add-member-results');
            const convId    = document.getElementById('add-member-conv-id').value;

            if (!data.data?.length) {
                container.innerHTML = `
                    <p class="text-xs text-gray-400 text-center py-3 italic">
                        No users found
                    </p>`;
                return;
            }

            container.innerHTML = data.data.map(u => `
                <button onclick="addMemberToConversation(${convId}, ${u.id}, '${escapeHtmlChat(u.name)}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5
                               rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700
                               transition text-left">
                    <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center
                                justify-center text-white font-bold text-xs flex-shrink-0">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            ${escapeHtmlChat(u.name)}
                        </p>
                        <p class="text-xs text-gray-400">${escapeHtmlChat(u.email)}</p>
                    </div>
                </button>`
            ).join('');

        } catch {
            showChatToast('Search failed.', 'error');
        }
    }, 300);
}

async function addMemberToConversation(conversationId, userId, userName) {
    try {
        const res  = await fetch(
            `/chat/conversations/${conversationId}/members`,
            {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
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
    const q     = query.trim().toLowerCase();

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
    let   total  = 0;

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
    const html    = `
    <a href="/chat/conversations/${conv.id}"
       id="conv-item-${conv.id}"
       class="conv-item flex items-center gap-3 px-4 py-3 cursor-pointer
              hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
              border-b border-gray-50 dark:border-gray-700/50"
       data-name="${escapeHtmlChat((conv.name || '').toLowerCase())}">
        <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center
                    justify-center text-white font-bold text-sm flex-shrink-0">
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
        top:      thread.scrollHeight,
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
    lightbox.id    = 'chat-lightbox';
    lightbox.className = 'fixed inset-0 z-[9999] flex items-center justify-center px-4';
    lightbox.style.backgroundColor = 'rgba(0,0,0,0.88)';
    lightbox.innerHTML = `
        <div class="relative max-w-4xl w-full">
            <button onclick="document.getElementById('chat-lightbox').remove()"
                    class="absolute -top-10 right-0 text-white/70
                           hover:text-white text-3xl font-bold transition">
                &times;
            </button>
            <img src="${url}" alt="Full size"
                 class="w-full max-h-[85vh] object-contain rounded-xl">
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
    toast.className = 'chat-toast fixed bottom-6 right-6 z-[9999] px-4 py-3 '
        + 'rounded-xl text-white text-sm font-medium shadow-xl '
        + 'transition-all duration-300 '
        + (type === 'error' ? 'bg-red-600' : 'bg-gray-900 dark:bg-gray-700');
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateY(8px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// UTILITY
// ============================================================

function escapeHtmlChat(str) {
    if (!str) return '';
    const div   = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
}