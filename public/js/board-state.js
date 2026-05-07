const boardStateElement = document.getElementById('board-state');

if (boardStateElement) {
    const boardId = boardStateElement.dataset.boardId;
    const boardOwnerId = parseInt(boardStateElement.dataset.boardOwnerId, 10);
    const currentUserId = parseInt(boardStateElement.dataset.currentUserId, 10);

    async function refreshBoardState() {
        try {
            const response = await fetch(`/boards/${boardId}/state`, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            if (!data.success) {
                return;
            }

            updatePendingInvites(data.invited_members);
            updateMembersList(data.members);
        } catch (error) {
            console.error('Board state refresh failed:', error);
        }
    }

    function updatePendingInvites(invites) {
        const list = document.getElementById('pending-invitations-list');
        const empty = document.getElementById('pending-invitations-empty');

        if (!list || !empty) {
            return;
        }

        if (invites.length === 0) {
            list.innerHTML = '';
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');
        list.innerHTML = invites.map(invite => buildInviteRow(invite)).join('');
    }

    function buildInviteRow(invite) {
        const cancelButton = currentUserId === boardOwnerId
            ? `<form method="POST" action="/boards/${boardId}/members/${invite.id}">
                    <input type="hidden" name="_token" value="${getCsrfToken()}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button"
                        onclick="handleCancelInviteJS('${escapeHtml(invite.name)}', this.form)"
                        class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                        Cancel
                    </button>
                </form>`
            : '';

        return `
            <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-yellow-700 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        ${escapeHtml(invite.name.charAt(0).toUpperCase())}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">${escapeHtml(invite.name)}</p>
                        <p class="text-xs text-gray-400">${escapeHtml(invite.email)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                        Pending
                    </span>
                    ${cancelButton}
                </div>
            </div>`;
    }

    function updateMembersList(members) {
        const list = document.getElementById('board-members-list');
        if (!list) {
            return;
        }

        list.innerHTML = members.map(member => buildMemberRow(member)).join('');
    }

    function buildMemberRow(member) {
        const roleBadge = member.role === 'owner'
            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';

        const removeButton = member.role !== 'owner' && currentUserId === boardOwnerId
            ? `
                <form method="POST" action="/boards/${boardId}/members/${member.id}">
                    <input type="hidden" name="_token" value="${getCsrfToken()}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button"
                        onclick="handleRemoveMemberJS('${escapeHtml(member.name)}', this.form)"
                        class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                        Remove
                    </button>
                </form>`
            : '';

        return `
            <div class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-700 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-700 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        ${escapeHtml(member.name.charAt(0).toUpperCase())}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">${escapeHtml(member.name)}</p>
                        <p class="text-xs text-gray-400">${escapeHtml(member.email)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full ${roleBadge}">
                        ${escapeHtml(member.role.charAt(0).toUpperCase() + member.role.slice(1))}
                    </span>
                    ${removeButton}
                </div>
            </div>`;
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    refreshBoardState();
    setInterval(refreshBoardState, 5000);
}

// Warning Modal Helper Function
async function showWarningModal(options) {
    const modal = document.getElementById('warning-modal');
    if (!modal) {
        return window.confirm(options.message || 'Are you sure you want to proceed?');
    }

    const modalData = modal.__x;
    if (!modalData || !modalData.$data || typeof modalData.$data.show !== 'function') {
        return window.confirm(options.message || 'Are you sure you want to proceed?');
    }

    return await modalData.$data.show(options);
}

async function handleCancelInviteJS(name, form) {
    const confirmed = await showWarningModal({
        title: 'Cancel Invitation',
        message: `Cancel invitation to ${name}?`,
        warningText: 'The user will no longer be able to join this board.',
        confirmText: 'Cancel Invitation'
    });

    if (confirmed) {
        form.submit();
    }
}

async function handleRemoveMemberJS(name, form) {
    const confirmed = await showWarningModal({
        title: 'Remove Member',
        message: `Remove ${name} from this board?`,
        warningText: 'They will lose access to all board content and their cards will remain.',
        confirmText: 'Remove Member'
    });

    if (confirmed) {
        form.submit();
    }
}
