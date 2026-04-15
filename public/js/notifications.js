
const notifCsrf = document.querySelector('meta[name="csrf-token"]')?.content;

async function loadNotifications() {
    const list = document.getElementById('notif-list');
    if (!list) return;

    try {
        const res = await fetch('/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': notifCsrf,
            },
        });
        const data = await res.json();

        if (!data.success) return;

        updateBadge(data.unread_count);

        if (data.notifications.length === 0) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 px-4">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full
                                flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                                     14.158V11a6.002 6.002 0 00-4-5.659V5a2 2
                                     0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159
                                     c0 .538-.214 1.055-.595 1.436L4 17h5m6
                                     0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        All caught up!
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        No notifications yet
                    </p>
                </div>`;
            return;
        }

        list.innerHTML = data.notifications.map(n => buildNotifHTML(n)).join('');

    } catch (err) {
        console.error('Notification load error:', err);
    }
}

function buildNotifHTML(n) {
    const timeAgo = formatTimeAgo(n.created_at);
    const isUnread = !n.is_read;
    const initials = n.actor?.name?.charAt(0).toUpperCase() || '?';

    const iconMap = {
        assigned_card: { bg: 'bg-blue-100 dark:bg-blue-900/40', color: 'text-blue-600 dark:text-blue-400', path: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
        moved_card: { bg: 'bg-purple-100 dark:bg-purple-900/40', color: 'text-purple-600 dark:text-purple-400', path: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
        new_comment: { bg: 'bg-green-100 dark:bg-green-900/40', color: 'text-green-600 dark:text-green-400', path: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' },
        added_to_board: { bg: 'bg-yellow-100 dark:bg-yellow-900/40', color: 'text-yellow-600 dark:text-yellow-400', path: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' },
        completed_card: { bg: 'bg-green-100 dark:bg-green-900/40', color: 'text-green-600 dark:text-green-400', path: 'M5 13l4 4L19 7' },
        reopened_card: { bg: 'bg-yellow-100 dark:bg-yellow-900/40', color: 'text-yellow-600 dark:text-yellow-400', path: 'M12 8v4l3 3M12 4a8 8 0 100 16 8 8 0 000-16z' },
        removed_from_board: { bg: 'bg-red-100 dark:bg-red-900/40', color: 'text-red-600 dark:text-red-400', path: 'M6 18L18 6M6 6l12 12' },
    };

    const icon = iconMap[n.type] || iconMap['moved_card'];

    return `
    <div class="flex items-start gap-3 px-4 py-3 cursor-pointer
                hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                ${isUnread ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''}"
         id="notif-${n.id}"
         onclick="handleNotifClick(${n.id}, '${n.url || ''}')">

        <div class="relative flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-blue-700 flex items-center
                        justify-center text-white text-sm font-bold">
                ${initials}
            </div>
            <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full
                        flex items-center justify-center ${icon.bg}">
                <svg class="w-2.5 h-2.5 ${icon.color}" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2.5" d="${icon.path}"/>
                </svg>
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-700 dark:text-gray-200 leading-relaxed
                      ${isUnread ? 'font-medium' : ''}">
                ${escapeHtmlNotif(n.message)}
            </p>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    ${timeAgo}
                </span>
                ${n.board ? `<span class="text-xs text-gray-300 dark:text-gray-600">·</span>
                <span class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[100px]">
                    ${escapeHtmlNotif(n.board.name)}
                </span>` : ''}
            </div>
        </div>

        <div class="flex-shrink-0 mt-1">
            ${isUnread
            ? `<div class="w-2 h-2 bg-blue-500 rounded-full" id="notif-dot-${n.id}"></div>`
            : `<div class="w-2 h-2" id="notif-dot-${n.id}"></div>`
        }
        </div>
    </div>`;
}

async function handleNotifClick(notifId, url) {
    try {
        await fetch(`/notifications/${notifId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': notifCsrf,
                'Accept': 'application/json',
            },
        });

        const row = document.getElementById(`notif-${notifId}`);
        if (row) {
            row.classList.remove('bg-blue-50/50', 'dark:bg-blue-900/10');
            const dot = document.getElementById(`notif-dot-${notifId}`);
            if (dot) dot.classList.remove('bg-blue-500');
        }

        decreaseBadge();

        if (url && url !== 'null' && url !== '') {
            window.location.href = url;
        }
    } catch (err) {
        console.error('Mark read error:', err);
    }
}

async function markAllRead() {
    try {
        await fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': notifCsrf,
                'Accept': 'application/json',
            },
        });

        document.querySelectorAll('[id^="notif-"]').forEach(row => {
            row.classList.remove('bg-blue-50/50', 'dark:bg-blue-900/10');
        });
        document.querySelectorAll('[id^="notif-dot-"]').forEach(dot => {
            dot.classList.remove('bg-blue-500');
        });
        document.querySelectorAll('p[class*="font-medium"]').forEach(p => {
            p.classList.remove('font-medium');
        });

    
        updateBadge(0);

    } catch (err) {
        console.error('Mark all read error:', err);
    }
}


function updateBadge(count) {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;
    if (count <= 0) {
        badge.classList.add('hidden');
        badge.textContent = '';
    } else {
        badge.classList.remove('hidden');
        badge.textContent = count > 9 ? '9+' : count;
    }
}

function decreaseBadge() {
    const badge = document.getElementById('notif-badge');
    if (!badge || badge.classList.contains('hidden')) return;
    const current = parseInt(badge.textContent) || 1;
    updateBadge(current - 1);
}


function formatTimeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds

    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';

    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}


function escapeHtmlNotif(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}


setInterval(async () => {
    try {
        const res = await fetch('/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': notifCsrf,
            },
        });
        const data = await res.json();
        if (data.success) {
            updateBadge(data.unread_count);
        }
    } catch { }
}, 10000);