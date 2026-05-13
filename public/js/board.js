const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
async function fetchJSON(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    };
    if (body) options.body = JSON.stringify(body);

    const response = await fetch(url, options);
    const data = await response.json();

    if (!response.ok) {
        const msg = data.message || data.error || 'Something went wrong.';
        showToast(msg, 'error');
        throw new Error(msg);
    }
    return data;
}


function showToast(message, type = 'success') {
    
    document.querySelectorAll('.js-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'js-toast fixed bottom-6 right-6 z-[9999] px-4 py-3 rounded-xl '
        + 'text-white text-sm font-medium shadow-xl transition-all duration-300 '
        + (type === 'error' ? 'bg-red-600' : 'bg-gray-900');
    toast.textContent = message;
    document.body.appendChild(toast);

    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function getActivityTimestamp() {
    return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

async function openCardModal(cardId) {
    cardModalDirty = false;
    const modal = document.getElementById('card-modal');
    const body = document.getElementById('card-modal-body');

    if (!modal || !body) return;

    body.innerHTML = `
        <div class="flex items-center justify-center py-16">
            <div class="w-8 h-8 border-2 border-blue-600 border-t-transparent
                        rounded-full animate-spin"></div>
        </div>`;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        const response = await fetch(`/cards/${cardId}`, {
            headers: {
                'Accept': 'text/html',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) throw new Error('Failed to load card.');
        body.innerHTML = await response.text();

        setTimeout(() => {
            body.querySelectorAll('textarea, input').forEach(el => {
                el.addEventListener('mousedown', e => e.stopPropagation());
                el.addEventListener('click', e => {
                    e.stopPropagation();
                    el.focus();
                });
            });
        }, 50);

    } catch (err) {
        body.innerHTML = `<p class="text-center text-red-500 py-12 text-sm">
                              Failed to load card. Please try again.
                          </p>`;
    }
}

function closeCardModal() {
    const cardModal = document.getElementById('card-modal');
    if (cardModal) cardModal.classList.add('hidden');
    document.body.style.overflow = '';

   
    if (cardModalDirty) {
        cardModalDirty = false;
        window.location.reload();
    }
}


document.addEventListener('DOMContentLoaded', function () {
    const cardModal = document.getElementById('card-modal');
    if (cardModal) {
        cardModal.addEventListener('mousedown', function (e) {
            if (e.target === cardModal) closeCardModal();
        });
    }

    const labelsModal = document.getElementById('labels-modal');
    if (labelsModal) {
        labelsModal.addEventListener('click', function (e) {
            if (e.target === this) closeLabelsManager();
        });
    }
});


document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCardModal();
        closeLabelsManager();
    }
});


function openLabelsManager() {
    const labelsModal = document.getElementById('labels-modal');
    if (labelsModal) {
        labelsModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeLabelsManager() {
    const labelsModal = document.getElementById('labels-modal');
    if (labelsModal) labelsModal.classList.add('hidden');
    document.body.style.overflow = '';
}

async function createLabel(boardId) {
    const nameInput = document.getElementById('new-label-name');
    const colorInput = document.querySelector('input[name="label_color"]:checked');

    const name = nameInput.value.trim();
    const color = colorInput ? colorInput.value : '#0079BF';

    if (!name) {
        showToast('Please enter a label name.', 'error');
        nameInput.focus();
        return;
    }

    try {
        const data = await fetchJSON(`/boards/${boardId}/labels`, 'POST', { name, color });

        if (data.success) {
            const noMsg = document.getElementById('no-labels-msg');
            if (noMsg) noMsg.remove();

            const list = document.getElementById('labels-list');
            const l = data.data;

            const row = document.createElement('div');
            row.id = `label-row-${l.id}`;
            row.className = 'flex items-center justify-between py-2 px-3 rounded-lg';
            row.style.backgroundColor = l.color + '20';
            row.innerHTML = `
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <span class="w-4 h-4 rounded-full flex-shrink-0"
                          id="label-dot-${l.id}"
                          style="background-color:${l.color}"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate"
                          id="label-name-${l.id}">
                        ${escapeHtml(l.name)}
                    </span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                    <button onclick="startEditLabel(${l.id}, '${escapeHtml(l.name)}', '${l.color}', ${boardId})"
                            class="w-6 h-6 flex items-center justify-center rounded
                                   text-gray-400 hover:text-blue-600 dark:hover:text-blue-400
                                   hover:bg-white/50 transition"
                            title="Edit label">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button onclick="deleteLabel(${l.id}, ${boardId})"
                            class="w-6 h-6 flex items-center justify-center rounded
                                   text-gray-400 hover:text-red-500 dark:hover:text-red-400
                                   hover:bg-white/50 transition"
                            title="Delete label">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>`;

            list.appendChild(row);
            addLabelOption(l);
            nameInput.value = '';
            showToast('Label created.');
        }
    } catch {
    }
}

function addLabelOption(label) {
    document.querySelectorAll('.js-label-select').forEach(select => {
        if (!select.querySelector(`option[value="${label.id}"]`)) {
            const option = document.createElement('option');
            option.value = label.id;
            option.textContent = label.name;
            select.appendChild(option);
        }
    });
}

function updateLabelOption(label) {
    document.querySelectorAll('.js-label-select').forEach(select => {
        const option = select.querySelector(`option[value="${label.id}"]`);
        if (option) {
            option.textContent = label.name;
        }
    });
}

function removeLabelOption(labelId) {
    document.querySelectorAll('.js-label-select').forEach(select => {
        const option = select.querySelector(`option[value="${labelId}"]`);
        if (option) option.remove();
    });
}

function startEditTitle(cardId, el) {
    const current = el.textContent.trim();
    el.dataset.original = current;

    const input = document.createElement('input');
    input.type = 'text';
    input.value = current;
    input.className = 'w-full text-xl font-bold text-gray-900 dark:text-white '
        + 'bg-white dark:bg-gray-700 border border-blue-400 rounded-lg '
        + 'px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500';

    el.replaceWith(input);
    input.focus();
    input.select();

    async function finishEdit() {
        const newTitle = input.value.trim();

        if (!newTitle) {
            // Revert if empty
            input.replaceWith(el);
            return;
        }

        if (newTitle === current) {
            input.replaceWith(el);
            return;
        }

        try {
            await fetchJSON(`/cards/${cardId}`, 'PUT', { title: newTitle });
            el.textContent = newTitle;

           
            const boardCard = document.querySelector(`[data-id="${cardId}"] p`);
            if (boardCard) boardCard.textContent = newTitle;
            cardModalDirty = true;
            showToast('Title updated.');

            // Add activity log for title change
            const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
            const activityDiv = document.getElementById(`activity-logs-${cardId}`);
            if (activityDiv) {
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            ${userName} changed the title to '${newTitle}'
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        } catch {
            el.textContent = current;
        }

        input.replaceWith(el);
    }

    input.addEventListener('blur', finishEdit);
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { input.value = current; input.blur(); }
    });
}


async function saveCardField(cardId, field, value) {
    try {
        await fetchJSON(`/cards/${cardId}`, 'PUT', { [field]: value || null });

        cardModalDirty = true;
        if (field === 'description') showToast('Description saved.');
        if (field === 'due_date') {
            showToast('Due date saved.');
            refreshCardDueStatus(cardId);
        }
        if (field === 'cover_color') {
            showToast('Cover updated.');
           
            const tile = document.getElementById(`card-${cardId}`);
            if (tile) {
                let strip = tile.querySelector('.cover-strip');
                if (value) {
                    if (!strip) {
                        strip = document.createElement('div');
                        strip.className = 'cover-strip h-8 rounded-t-lg w-full';
                        tile.prepend(strip);
                    }
                    strip.style.backgroundColor = value;
                } else if (strip) {
                    strip.remove();
                }
            }
        }

        // Add activity log for the change
        const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
        const activityDiv = document.getElementById(`activity-logs-${cardId}`);
        if (activityDiv) {
            let description = '';
            if (field === 'description') {
                description = `${userName} updated the description`;
            } else if (field === 'due_date') {
                description = value ? `${userName} set the due date to ${new Date(value).toLocaleDateString()}` : `${userName} removed the due date`;
            } else if (field === 'cover_color') {
                description = value ? `${userName} changed the cover color` : `${userName} removed the cover`;
            }
            if (description) {
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">${description}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        }
    } catch {
        
    }
}

function getDueDateStatus(dateString) {
    if (!dateString) return null;

    const parts = dateString.split('-').map(Number);
    if (parts.length !== 3 || parts.some(isNaN)) return null;

    const [year, month, day] = parts;
    const dueDate = new Date(year, month - 1, day);
    dueDate.setHours(0, 0, 0, 0);

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (dueDate.getTime() < today.getTime()) {
        return { text: '⚠ Overdue', className: 'text-red-500' };
    }

    if (dueDate.getTime() === today.getTime()) {
        return { text: '⏰ Due today', className: 'text-yellow-600 dark:text-yellow-400' };
    }

    return { text: '✓ Upcoming', className: 'text-gray-400' };
}

function refreshCardDueStatus(cardId) {
    const dueInput = document.getElementById(`due-date-${cardId}`);
    const statusEl = document.getElementById(`due-date-status-${cardId}`);
    if (!dueInput || !statusEl) return;

    const status = getDueDateStatus(dueInput.value);
    if (!status) {
        statusEl.style.display = 'none';
        statusEl.textContent = '';
        return;
    }

    statusEl.style.display = 'block';
    statusEl.textContent = status.text;
    statusEl.className = `text-xs mt-1.5 font-medium ${status.className}`;
}

function refreshAllCardDueStatuses() {
    document.querySelectorAll('[id^="due-date-"]').forEach(input => {
        const match = input.id.match(/^due-date-(\d+)$/);
        if (match) refreshCardDueStatus(match[1]);
    });
}

if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function () {
        refreshAllCardDueStatuses();
        setInterval(refreshAllCardDueStatuses, 60000);
    });
}

async function deleteCard(cardId) {
    const confirmed = await showWarningModal({
        title: 'Delete Card',
        message: 'Delete this card permanently?',
        warningText: 'This cannot be undone.',
        confirmText: 'Delete Card'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/cards/${cardId}`, 'DELETE');


        const tile = document.getElementById(`card-${cardId}`);
        if (tile) tile.remove();

        closeCardModal();
        showToast('Card deleted.');
    } catch {
       
    }
}


async function postComment(cardId) {
    const textarea = document.getElementById(`new-comment-${cardId}`);
    const body = textarea.value.trim();

    if (!body) {
        showToast('Comment cannot be empty.', 'error');
        textarea.focus();
        return;
    }

    try {
        const data = await fetchJSON(`/cards/${cardId}/comments`, 'POST', { body });

        if (data.success) {
            const c = data.comment;
            const list = document.getElementById(`comments-list-${cardId}`);

            
            const placeholder = list.querySelector('p.italic');
            if (placeholder) placeholder.remove();

           
            const div = document.createElement('div');
            div.id = `comment-${c.id}`;
            div.className = 'flex gap-3';
            div.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-blue-700 flex-shrink-0
                            flex items-center justify-center text-white text-xs font-bold mt-0.5">
                    ${c.author.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            ${c.author.name}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">just now</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl px-3 py-2.5
                                text-sm text-gray-700 dark:text-gray-200 leading-relaxed">
                        ${escapeHtml(c.body)}
                    </div>
                    <button onclick="deleteComment(${c.id})"
                            class="text-xs text-gray-400 dark:text-gray-500
                                   hover:text-red-500 dark:hover:text-red-400 mt-1 transition">
                        Delete
                    </button>
                </div>`;

            list.appendChild(div);
            textarea.value = '';
            cardModalDirty = true;
            showToast('Comment posted.');

            // Add activity log for the comment
            const activityDiv = document.getElementById(`activity-logs-${cardId}`);
            if (activityDiv) {
                const cardTitle = document.querySelector(`[data-card-id="${cardId}"] h2`).textContent.trim();
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${c.author.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            ${c.author.name} commented on '${cardTitle}'
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        }
    } catch {
      
    }
}

async function deleteComment(commentId) {
    const confirmed = await showWarningModal({
        title: 'Delete Comment',
        message: 'Delete this comment?',
        confirmText: 'Delete Comment'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/comments/${commentId}`, 'DELETE');
        document.getElementById(`comment-${commentId}`)?.remove();
        cardModalDirty = true;
        showToast('Comment deleted.');
    } catch {
       
    }
}

async function assignUser(cardId, userId) {
    if (!userId) return;

    try {
        const data = await fetchJSON(`/cards/${cardId}/assign`, 'POST', {
            user_id: parseInt(userId),
        });

        if (data.success) {
           
            const container = document.getElementById(`assignees-${cardId}`);
            if (container) {
                const visible = data.assignees.slice(0, 4);
                const overflow = data.assignees.length - 4;

                container.innerHTML = visible.map(u => `
                    <div class="w-7 h-7 rounded-full bg-blue-700
                                flex items-center justify-center
                                text-white text-xs font-bold"
                         title="${u.name}"
                         id="assignee-avatar-${u.id}">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>`).join('');

                if (overflow > 0) {
                    container.innerHTML += `
                        <div class="w-7 h-7 rounded-full bg-gray-300 dark:bg-gray-600
                                    flex items-center justify-center
                                    text-gray-700 dark:text-gray-200 text-xs font-bold">
                            +${overflow}
                        </div>`;
                }
            }

        
            const tileAvatars = document.querySelector(
                `[data-id="${cardId}"] .assignee-avatars`
            );
            if (tileAvatars) {
                const visibleTile = data.assignees.slice(0, 4);
                const overflowTile = data.assignees.length - 4;

                tileAvatars.innerHTML = visibleTile.map(u => `
                    <div class="w-6 h-6 rounded-full bg-blue-700 ring-2
                                ring-white dark:ring-gray-800
                                flex items-center justify-center
                                text-white text-xs font-bold"
                         title="${u.name}">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>`).join('');

                if (overflowTile > 0) {
                    tileAvatars.innerHTML += `
                        <div class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600
                                    ring-2 ring-white dark:ring-gray-800
                                    flex items-center justify-center
                                    text-gray-600 dark:text-gray-300 text-xs font-bold">
                            +${overflowTile}
                        </div>`;
                }
            }

           
            const select = document.querySelector(`select[onchange*="assignUser(${cardId}"]`);
            if (select) {
                [...select.options].forEach(opt => {
                    if (!opt.value) return;
                    const isAssigned = data.assignees.some(u => u.id == opt.value);
                    opt.text = opt.text.replace(' ✓', '');
                    if (isAssigned) opt.text += ' ✓';
                });
            }

            cardModalDirty = true;
            showToast('Member assignment updated.');
        }
    } catch {
    
    }
}


async function attachLabel(cardId, labelId) {
    if (!labelId) return;

    try {
        const data = await fetchJSON(
            `/cards/${cardId}/labels/${labelId}`, 'POST'
        );

        if (data.success) {
            // Rebuild label chips in modal
            rebuildLabelChips(cardId, data.labels);
            cardModalDirty = true;
            showToast('Label added.');

            // Add activity log for label attach
            const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
            const cardTitle = document.querySelector(`[data-card-id="${cardId}"] h2`).textContent.trim();
            const select = document.getElementById(`card-label-select-${cardId}`);
            const option = select.querySelector(`option[value="${labelId}"]`);
            const labelName = option ? option.textContent.trim() : 'Label';
            const activityDiv = document.getElementById(`activity-logs-${cardId}`);
            if (activityDiv) {
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            ${userName} attached label '${labelName}' to '${cardTitle}'
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        }
    } catch {
    }
}

async function detachLabel(cardId, labelId, labelName) {
    try {
        const data = await fetchJSON(
            `/cards/${cardId}/labels/${labelId}`, 'DELETE'
        );

        if (data.success) {
            rebuildLabelChips(cardId, data.labels);
            cardModalDirty = true;
            showToast('Label removed.');

            // Add activity log for label detach
            const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
            const cardTitle = document.querySelector(`[data-card-id="${cardId}"] h2`).textContent.trim();
            const activityDiv = document.getElementById(`activity-logs-${cardId}`);
            if (activityDiv) {
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            ${userName} removed label '${labelName}' from '${cardTitle}'
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        }
    } catch {
      
    }
}

function rebuildLabelChips(cardId, labels) {
    const container = document.getElementById(`card-labels-${cardId}`);
    if (!container) return;

    container.innerHTML = labels.map(l => `
        <span class="inline-flex items-center gap-1 text-xs font-medium
                     text-white px-2 py-0.5 rounded-full cursor-pointer
                     hover:opacity-80 transition"
              style="background-color:${l.color}"
              id="card-label-${cardId}-${l.id}"
              onclick="detachLabel(${cardId}, ${l.id})"
              title="Click to remove">
            ${escapeHtml(l.name)}
        </span>`).join('');
}

async function uploadAttachment(cardId, input) {
    const file = input.files[0];
    if (!file) return;

    // 10 MB size check client-side
    if (file.size > 10 * 1024 * 1024) {
        showToast('File too large. Maximum size is 10 MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', csrfToken);

    try {
        showToast('Uploading...');

        const response = await fetch(`/cards/${cardId}/attachments`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' },
        });

        const data = await response.json();

        if (!response.ok) {
            showToast(data.message || 'Upload failed.', 'error');
            return;
        }

        if (data.success) {
            const att = data.attachment;
            const container = document.getElementById(`attachments-${cardId}`);

            if (container) {
                const row = document.createElement('div');
                row.id = `att-${att.id}`;
                row.className = 'flex items-center justify-between bg-gray-50 '
                    + 'dark:bg-gray-700 rounded-lg px-2 py-1.5 group';
                row.innerHTML = `
                    <a href="${att.url}" target="_blank"
                       class="text-xs text-blue-600 dark:text-blue-400
                              hover:underline truncate max-w-[80px]"
                       title="${att.filename}">
                        ${att.filename.length > 14
                        ? att.filename.substring(0, 14) + '…'
                        : att.filename}
                    </a>
                    <button onclick="deleteAttachment(${att.id})"
                            class="text-gray-300 dark:text-gray-600
                                   hover:text-red-500 dark:hover:text-red-400
                                   text-base leading-none ml-1 flex-shrink-0 transition">
                        &times;
                    </button>`;
                container.appendChild(row);
            }

            input.value = '';
            cardModalDirty = true;
            showToast('File uploaded.');

            // Add activity log for attachment upload
            const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
            const activityDiv = document.getElementById(`activity-logs-${cardId}`);
            if (activityDiv) {
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            ${userName} attached '${att.filename}'
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        }
    } catch {
        showToast('Upload failed. Please try again.', 'error');
    }
}

async function deleteAttachment(attId) {
    const confirmed = await showWarningModal({
        title: 'Remove Attachment',
        message: 'Remove this attachment?',
        confirmText: 'Remove Attachment'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/attachments/${attId}`, 'DELETE');
        const attDiv = document.getElementById(`att-${attId}`);
        const filename = attDiv ? attDiv.querySelector('a').textContent.trim() : 'Attachment';
        attDiv?.remove();
        cardModalDirty = true;
        showToast('Attachment removed.');

        // Add activity log for attachment removal
        const cardId = document.querySelector('[data-card-id]').dataset.cardId;
        const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
        const cardTitle = document.querySelector(`[data-card-id="${cardId}"] h2`).textContent.trim();
        const activityDiv = document.getElementById(`activity-logs-${cardId}`);
        if (activityDiv) {
            const activityItem = document.createElement('div');
            activityItem.className = 'flex items-start gap-2.5';
            activityItem.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                            flex-shrink-0 flex items-center justify-center
                            text-gray-600 dark:text-gray-300 text-xs font-bold">
                    ${userName.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                        ${userName} removed attachment '${filename}'
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                </div>
            `;
            activityDiv.insertBefore(activityItem, activityDiv.firstChild);
        }
    } catch {

    }
}

function showAddListForm() {
    document.getElementById('add-list-btn').classList.add('hidden');
    document.getElementById('add-list-form').classList.remove('hidden');
    document.getElementById('new-list-name').focus();
}

function hideAddListForm() {
    document.getElementById('add-list-btn').classList.remove('hidden');
    document.getElementById('add-list-form').classList.add('hidden');
    document.getElementById('new-list-name').value = '';
}

async function storeList(boardId) {
    const input = document.getElementById('new-list-name');
    const name = input.value.trim();

    if (!name) {
        showToast('Please enter a list name.', 'error');
        input.focus();
        return;
    }

    try {
        const data = await fetchJSON(`/boards/${boardId}/lists`, 'POST', { name });

        if (data.success) {
            const html = buildListHTML(data.list, boardId);
            const container = document.getElementById('add-list-container');
            container.insertAdjacentHTML('beforebegin', html);

           
            initCardsSortable(data.list.id);

            hideAddListForm();
            showToast('List added.');
        }
    } catch {

    }
}

function inlineEditList(listId, el) {
    const original = el.textContent.trim();

    const input = document.createElement('input');
    input.type = 'text';
    input.value = original;
    input.className = 'w-full font-semibold text-sm text-gray-800 dark:text-gray-100 '
        + 'bg-white dark:bg-gray-800 border border-blue-400 rounded-lg '
        + 'px-2 py-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500';

    el.replaceWith(input);
    input.focus();
    input.select();

    async function saveListName() {
        const newName = input.value.trim();

        if (!newName) {
            input.replaceWith(el);
            return;
        }

        if (newName === original) {
            input.replaceWith(el);
            return;
        }

        try {
            const boardId = getBoardIdFromUrl();
            const data = await fetchJSON(
                `/boards/${boardId}/lists/${listId}`, 'PUT', { name: newName }
            );
            el.textContent = data.list.name;
            showToast('List renamed.');
        } catch {
            el.textContent = original;
        }

        input.replaceWith(el);
    }

    input.addEventListener('blur', saveListName);
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { input.value = original; input.blur(); }
    });
}

async function archiveList(listId, boardId) {
    const confirmed = await showWarningModal({
        title: 'Archive List',
        message: 'Archive this list?',
        warningText: 'Cards will be preserved.',
        confirmText: 'Archive List'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/boards/${boardId}/lists/${listId}`, 'PUT', {
            is_archived: true,
        });
        document.getElementById(`list-${listId}`)?.remove();
        showToast('List archived.');
    } catch {
    }
}

async function unarchiveList(listId, boardId) {
    const confirmed = await showWarningModal({
        title: 'Unarchive List',
        message: 'Unarchive this list?',
        confirmText: 'Unarchive List'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/boards/${boardId}/lists/${listId}`, 'PUT', {
            is_archived: false,
        });
        showToast('List unarchived.');
        window.location.reload();
    } catch {
    }
}

async function deleteList(listId, boardId) {
    const confirmed = await showWarningModal({
        title: 'Delete List',
        message: 'Delete this list and ALL its cards?',
        warningText: 'This cannot be undone.',
        confirmText: 'Delete List'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/boards/${boardId}/lists/${listId}`, 'DELETE');
        document.getElementById(`list-${listId}`)?.remove();
        showToast('List deleted.');
    } catch {
    }
}

function showAddCardForm(listId) {
    document.getElementById(`add-card-btn-${listId}`).classList.add('hidden');
    document.getElementById(`add-card-form-${listId}`).classList.remove('hidden');
    setTimeout(() => {
        const textarea = document.getElementById(`new-card-title-${listId}`);
        textarea.value = '';
        textarea.focus();
    }, 50);
}

function hideAddCardForm(listId) {
    document.getElementById(`add-card-btn-${listId}`).classList.remove('hidden');
    document.getElementById(`add-card-form-${listId}`).classList.add('hidden');
    document.getElementById(`new-card-title-${listId}`).value = '';
}

async function storeCard(listId) {
    const textarea = document.getElementById(`new-card-title-${listId}`);
    const title = textarea.value.trim();

    if (!title) {
        showToast('Please enter a card title.', 'error');
        textarea.focus();
        return;
    }

    try {
        const data = await fetchJSON(`/lists/${listId}/cards`, 'POST', { title });

        if (data.success) {
            const container = document.getElementById(`cards-${listId}`);
            const div = document.createElement('div');

            div.className = 'card-item bg-white dark:bg-gray-800 rounded-lg shadow-sm '
                + 'border border-gray-200 dark:border-gray-600 cursor-pointer '
                + 'hover:shadow-md hover:border-gray-300 dark:hover:border-gray-500 '
                + 'transition-all group';
            div.dataset.id = data.card.id;
            div.id = `card-${data.card.id}`;
            div.setAttribute('onclick', `openCardModal(${data.card.id})`);
            div.innerHTML = `
                <div class="p-3">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 leading-snug
                              group-hover:text-blue-700 dark:group-hover:text-blue-400 transition-colors">
                        ${escapeHtml(data.card.title)}
                    </p>
                </div>`;

            container.appendChild(div);
            textarea.value = '';
            hideAddCardForm(listId);
            showToast('Card added.');
        }
    } catch {
    }
}

function initCardsSortable(listId) {
    const el = document.getElementById(`cards-${listId}`);
    if (!el || el.dataset.sortableInit) return;

    el.dataset.sortableInit = '1';

    Sortable.create(el, {
        group: 'shared-cards',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',

        onEnd: async function (evt) {
            const cardId = evt.item.dataset.id;
            const newListId = evt.to.dataset.listId;
            const newPos = evt.newIndex;

            if (evt.from === evt.to && evt.oldIndex === evt.newIndex) return;

            try {
                await fetchJSON(`/cards/${cardId}/move`, 'POST', {
                    list_id: parseInt(newListId),
                    position: newPos,
                });
            } catch {
              
                evt.from.insertBefore(
                    evt.item,
                    evt.from.children[evt.oldIndex] || null
                );
                showToast('Move failed — reverted.', 'error');
            }
        },
    });
}

function initListsSortable(boardId) {
    const board = document.getElementById('board-columns');
    if (!board) return;

    Sortable.create(board, {
        animation: 150,
        handle: '.list-column',
        filter: '#add-list-container',
        ghostClass: 'sortable-ghost',

        onEnd: async function () {
            const lists = [...board.querySelectorAll('.list-column')]
                .map((col, index) => ({
                    id: parseInt(col.dataset.id),
                    position: index,
                }));

            try {
                await fetchJSON(
                    `/boards/${boardId}/lists/reorder`, 'POST', { lists }
                );
            } catch {
                showToast('Reorder failed.', 'error');
            }
        },
    });
}


function getBoardIdFromUrl() {
    return window.location.pathname.split('/')[2];
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function buildListHTML(list, boardId) {
    return `
    <div class="list-column flex-shrink-0 w-72 rounded-xl flex flex-col"
         style="max-height: calc(100vh - 130px);"
         data-id="${list.id}"
         id="list-${list.id}">

        <div class="flex items-center justify-between px-3 pt-3 pb-2
                    bg-gray-200 dark:bg-gray-700 rounded-t-xl">
            <h3 class="list-title font-semibold text-sm text-gray-800 dark:text-gray-100
                       flex-1 mr-2 cursor-pointer truncate"
                ondblclick="inlineEditList(${list.id}, this)">
                ${escapeHtml(list.name)}
            </h3>
            <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">0</span>
            <button onclick="deleteList(${list.id}, ${boardId})"
                    class="w-6 h-6 flex items-center justify-center rounded
                           text-gray-500 dark:text-gray-400
                           hover:bg-gray-300 dark:hover:bg-gray-600
                           hover:text-red-500 transition text-lg leading-none">
                &times;
            </button>
        </div>

        <div class="cards-container flex-1 overflow-y-auto px-2 py-2 space-y-2
                    bg-gray-200 dark:bg-gray-700"
             id="cards-${list.id}"
             data-list-id="${list.id}"
             style="min-height: 48px;">
        </div>

        <div class="bg-gray-200 dark:bg-gray-700 rounded-b-xl px-2 pb-2 pt-1">
            <div id="add-card-form-${list.id}" class="hidden mb-1">
                <textarea id="new-card-title-${list.id}"
                          placeholder="Enter a title for this card..."
                          rows="3"
                          maxlength="255"
                          class="w-full border border-gray-300 dark:border-gray-600
                                 rounded-lg p-2.5 text-sm resize-none
                                 bg-white dark:bg-gray-800
                                 text-gray-900 dark:text-gray-100
                                 placeholder-gray-400
                                 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 focus:border-transparent mb-2"
                          onkeydown="if(event.key==='Enter'&&!event.shiftKey){
                                         event.preventDefault();
                                         storeCard(${list.id});
                                     }
                                     if(event.key==='Escape'){
                                         hideAddCardForm(${list.id});
                                     }"></textarea>
                <div class="flex items-center gap-2">
                    <button onclick="storeCard(${list.id})"
                            class="bg-blue-700 hover:bg-blue-800 text-white text-xs
                                   font-medium px-3 py-1.5 rounded-lg transition">
                        Add card
                    </button>
                    <button onclick="hideAddCardForm(${list.id})"
                            class="text-gray-500 dark:text-gray-400
                                   hover:text-gray-700 dark:hover:text-gray-200
                                   text-xl leading-none px-1 transition">
                        &times;
                    </button>
                </div>
            </div>
            <button id="add-card-btn-${list.id}"
                    onclick="showAddCardForm(${list.id})"
                    class="w-full flex items-center gap-1.5 text-left text-sm
                           text-gray-600 dark:text-gray-400
                           hover:text-gray-800 dark:hover:text-gray-200
                           hover:bg-gray-300 dark:hover:bg-gray-600
                           rounded-lg px-2 py-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add a card
            </button>
        </div>
    </div>`;
}

document.addEventListener('DOMContentLoaded', function () {
    const boardId = getBoardIdFromUrl();

    document.querySelectorAll('.cards-container').forEach(function (el) {
        initCardsSortable(el.dataset.listId);
    });

    initListsSortable(boardId);
});

async function toggleCardComplete(cardId, checkboxWrapper) {
    try {
        const response = await fetchJSON(`/cards/${cardId}/complete`, 'POST');

        if (response.success) {
            const data = response.data || {};
            const isCompleted = data.is_completed;
            const tile = document.getElementById(`card-${cardId}`);
            const circle = document.getElementById(`complete-circle-${cardId}`);
            const tick = document.getElementById(`complete-tick-${cardId}`);
            const title = tile?.querySelector('p.text-sm.font-medium');
            const badge = tile?.querySelector('.card-completed-badge');

            if (isCompleted) {
                circle?.classList.remove(
                    'bg-white/80', 'dark:bg-gray-700/80',
                    'border-gray-400', 'dark:border-gray-500',
                    'hover:border-green-400'
                );
                circle?.classList.add('bg-green-500', 'border-green-500');
                tick?.classList.remove('hidden');

                title?.classList.add('line-through', 'text-gray-400', 'dark:text-gray-500');
                title?.classList.remove('text-gray-800', 'dark:text-gray-100');

                badge?.classList.remove('hidden');
                tile?.setAttribute('data-completed', 'true');
                
                // Hide timer section when task is completed
                const timerSection = document.getElementById(`timer-section-${cardId}`);
                timerSection?.classList.add('hidden');
            } else {
                circle?.classList.add(
                    'bg-white/80', 'dark:bg-gray-700/80',
                    'border-gray-400', 'dark:border-gray-500',
                    'hover:border-green-400'
                );
                circle?.classList.remove('bg-green-500', 'border-green-500');
                tick?.classList.add('hidden');

                title?.classList.remove('line-through', 'text-gray-400', 'dark:text-gray-500');
                title?.classList.add('text-gray-800', 'dark:text-gray-100');

                badge?.classList.add('hidden');
                tile?.setAttribute('data-completed', 'false');
                
                // Show timer section when task is marked incomplete
                const timerSection = document.getElementById(`timer-section-${cardId}`);
                timerSection?.classList.remove('hidden');
            }

            showToast(response.message || (isCompleted ? 'Card marked as complete.' : 'Card marked as incomplete.'));
            
            // Update modal timer button if modal is open
            const cardModal = document.getElementById('card-modal');
            if (cardModal && !cardModal.classList.contains('hidden')) {
                const timerDisplay = document.getElementById(`modal-timer-display-${cardId}`);
                if (timerDisplay) {
                    const timerButtonElement = timerDisplay.nextElementSibling;
                    
                    if (isCompleted) {
                        // Stop timer if running
                        if (!timerDisplay.classList.contains('hidden')) {
                            stopTimeTracker(cardId);
                        }
                        // Replace with "Task completed" div
                        const completedDiv = document.createElement('div');
                        completedDiv.className = 'flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-2.5';
                        completedDiv.innerHTML = `
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Task completed</span>
                        `;
                        if (timerButtonElement) {
                            timerButtonElement.replaceWith(completedDiv);
                        }
                    } else {
                        // Replace with "Start Task" button
                        const startButton = document.createElement('button');
                        startButton.id = `modal-timer-btn-${cardId}`;
                        startButton.onclick = () => startTimeTracker(cardId);
                        startButton.className = 'w-full inline-flex items-center justify-center gap-2 text-sm font-medium bg-green-600 hover:bg-green-700 text-white px-3 py-2.5 rounded-xl transition shadow-sm';
                        startButton.innerHTML = `
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            Start Task
                        `;
                        if (timerButtonElement) {
                            timerButtonElement.replaceWith(startButton);
                        }
                    }
                }
            }
            
            applyBoardFilters();
            cardModalDirty = true;
        }
    } catch {
    }
}

let activeFilter = null;
let currentSearch = '';

function applyBoardFilters() {
    const cards = document.querySelectorAll('.card-item');
    const listColumns = document.querySelectorAll('.list-column');

    cards.forEach(card => {
        const title = card.dataset.title || '';
        const description = card.dataset.description || '';
        const completed = card.dataset.completed === 'true';

        const searchMatch = currentSearch === ''
            || title.includes(currentSearch)
            || description.includes(currentSearch);

        let filterMatch = true;
        if (activeFilter === 'completed') filterMatch = completed;
        if (activeFilter === 'incomplete') filterMatch = !completed;

        card.style.display = (searchMatch && filterMatch) ? '' : 'none';
    });

    listColumns.forEach(col => {
        const visibleCards = col.querySelectorAll('.card-item:not([style*="display: none"])');
        const totalCards = col.querySelectorAll('.card-item').length;
        let emptyMsg = col.querySelector('.list-empty-search-msg');

        // Update card count badge
        const countBadge = col.querySelector('.list-column > div > span:nth-child(2)');
        if (countBadge) {
            // Show filtered count when filters are active, otherwise show total count
            const displayCount = (currentSearch !== '' || activeFilter) ? visibleCards.length : totalCards;
            countBadge.textContent = displayCount;
        }

        if (visibleCards.length === 0 && (currentSearch !== '' || activeFilter)) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'list-empty-search-msg text-xs text-center '
                    + 'text-gray-600 dark:text-gray-300 py-4 italic';
                emptyMsg.textContent = 'No cards match';
                const container = col.querySelector('.cards-container');
                if (container) container.appendChild(emptyMsg);
            }
            emptyMsg.style.display = '';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const boardId = getBoardIdFromUrl();

    document.querySelectorAll('.cards-container').forEach(function (el) {
        initCardsSortable(el.dataset.listId);
    });

    initListsSortable(boardId);

    const searchInput = document.getElementById('board-card-search');
    const clearBtn = document.getElementById('card-search-clear');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value.trim().toLowerCase();
            clearBtn?.classList.toggle('hidden', currentSearch === '');
            applyBoardFilters();
        });
    }
});
function clearCardSearch() {
    const searchInput = document.getElementById('board-card-search');
    if (searchInput) {
        searchInput.value = '';
        currentSearch = '';
        document.getElementById('card-search-clear')?.classList.add('hidden');
        applyBoardFilters();
        searchInput.focus();
    }
}


function toggleFilter(type) {
    const completedBtn = document.getElementById('filter-completed');
    const incompleteBtn = document.getElementById('filter-incomplete');

    if (activeFilter === type) {
        activeFilter = null;
        completedBtn?.classList.remove('bg-white/40', 'text-white', 'font-semibold');
        incompleteBtn?.classList.remove('bg-white/40', 'text-white', 'font-semibold');
        completedBtn?.classList.add('bg-white/20', 'text-white/80');
        incompleteBtn?.classList.add('bg-white/20', 'text-white/80');
    } else {
        activeFilter = type;

        completedBtn?.classList.remove('bg-white/40', 'text-white', 'font-semibold');
        incompleteBtn?.classList.remove('bg-white/40', 'text-white', 'font-semibold');
        completedBtn?.classList.add('bg-white/20', 'text-white/80');
        incompleteBtn?.classList.add('bg-white/20', 'text-white/80');

        if (type === 'completed') {
            completedBtn?.classList.remove('bg-white/20', 'text-white/80');
            completedBtn?.classList.add('bg-white/40', 'text-white', 'font-semibold');
        } else {
            incompleteBtn?.classList.remove('bg-white/20', 'text-white/80');
            incompleteBtn?.classList.add('bg-white/40', 'text-white', 'font-semibold');
        }
    }

    applyBoardFilters();
}


async function deleteLabel(labelId, boardId) {
    const confirmed = await showWarningModal({
        title: 'Delete Label',
        message: 'Delete this label?',
        warningText: 'It will be removed from all cards.',
        confirmText: 'Delete Label'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/boards/${boardId}/labels/${labelId}`, 'DELETE');

        document.getElementById(`label-row-${labelId}`)?.remove();
        removeLabelOption(labelId);

        const list = document.getElementById('labels-list');
        if (list && list.querySelectorAll('[id^="label-row-"]').length === 0) {
            const msg = document.createElement('p');
            msg.id = 'no-labels-msg';
            msg.className = 'text-sm text-gray-400 text-center py-3';
            msg.textContent = 'No labels yet. Create one below.';
            list.appendChild(msg);
        }

        showToast('Label deleted.');
        cardModalDirty = true;

    } catch {
    }
}


function startEditLabel(labelId, name, color, boardId) {
    document.getElementById('edit-label-id').value = labelId;
    document.getElementById('edit-board-id').value = boardId;
    document.getElementById('edit-label-name').value = name;

    const radios = document.querySelectorAll('input[name="edit_label_color"]');
    radios.forEach(r => {
        r.checked = r.value.toLowerCase() === color.toLowerCase();
    });

    const form = document.getElementById('edit-label-form');
    form.classList.remove('hidden');
    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    document.getElementById('edit-label-name').focus();
}


function cancelEditLabel() {
    document.getElementById('edit-label-form').classList.add('hidden');
    document.getElementById('edit-label-name').value = '';
    document.getElementById('edit-label-id').value = '';
    document.getElementById('edit-board-id').value = '';
}


async function saveEditLabel() {
    const labelId = document.getElementById('edit-label-id').value;
    const boardId = document.getElementById('edit-board-id').value;
    const name = document.getElementById('edit-label-name').value.trim();
    const color = document.querySelector('input[name="edit_label_color"]:checked')?.value;

    if (!name) {
        showToast('Label name cannot be empty.', 'error');
        document.getElementById('edit-label-name').focus();
        return;
    }

    if (!color) {
        showToast('Please select a color.', 'error');
        return;
    }

    try {
        const data = await fetchJSON(
            `/boards/${boardId}/labels/${labelId}`,
            'PUT',
            { name, color }
        );

        if (data.success) {
            const row = document.getElementById(`label-row-${labelId}`);
            const dot = document.getElementById(`label-dot-${labelId}`);
            const span = document.getElementById(`label-name-${labelId}`);

            if (row) row.style.backgroundColor = color + '20';
            if (dot) dot.style.backgroundColor = color;
            if (span) span.textContent = name;

            updateLabelOption({ id: labelId, name });
            cancelEditLabel();

            showToast('Label updated.');
            cardModalDirty = true;
        }
    } catch {
    }
}


async function uploadCoverImage(cardId, input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        showToast('Image too large. Max 5MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrfToken);

    try {
        showToast('Uploading cover...');

        const res = await fetch(`/cards/${cardId}/cover-image`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok) {
            showToast(data.message || 'Upload failed.', 'error');
            return;
        }

        if (data.success) {
            // Always rebuild the preview content
            const preview = document.getElementById(`cover-image-preview-${cardId}`);
            if (preview) {
                preview.classList.remove('hidden');
                preview.innerHTML = `
                    <div class="relative mb-3 rounded-xl overflow-hidden group">
                        <img src="${data.cover_image_url}"
                             alt="Cover image"
                             class="w-full h-24 object-cover rounded-xl">
                        <button onclick="removeCoverImage(${cardId})"
                                class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50
                                       hover:bg-black/70 text-white rounded-full
                                       flex items-center justify-center text-sm
                                       transition opacity-0 group-hover:opacity-100">
                            &times;
                        </button>
                    </div>`;
            }

            // Update the card tile on the board
            const tile = document.getElementById(`card-${cardId}`);
            if (tile) {
                tile.querySelector('.cover-strip')?.remove();
                tile.querySelector('.tile-cover-img')?.remove();

                const coverImg = document.createElement('img');
                coverImg.className = 'tile-cover-img w-full h-24 object-cover rounded-t-lg cursor-pointer';
                coverImg.src = data.cover_image_url;
                coverImg.onclick = () => openCardModal(cardId);
                tile.prepend(coverImg);
            }

            input.value = '';
            cardModalDirty = true;
            showToast('Cover image set.');

            // Add activity log for cover image upload
            const userName = document.querySelector(`[data-card-id="${cardId}"]`).dataset.userName;
            const cardTitle = document.querySelector(`[data-card-id="${cardId}"] h2`).textContent.trim();
            const activityDiv = document.getElementById(`activity-logs-${cardId}`);
            if (activityDiv) {
                const activityItem = document.createElement('div');
                activityItem.className = 'flex items-start gap-2.5';
                activityItem.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                flex-shrink-0 flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                            ${userName} added a cover image to '${cardTitle}'
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${getActivityTimestamp()}</p>
                    </div>
                `;
                activityDiv.insertBefore(activityItem, activityDiv.firstChild);
            }
        }
    } catch {
        showToast('Upload failed. Please try again.', 'error');
    }
}

async function removeCoverImage(cardId) {
    try {
        await fetchJSON(`/cards/${cardId}/cover-image`, 'DELETE');

        const preview = document.getElementById(`cover-image-preview-${cardId}`);
        if (preview) preview.classList.add('hidden');

        const tile = document.getElementById(`card-${cardId}`);
        tile?.querySelector('.tile-cover-img')?.remove();

        cardModalDirty = true;
        showToast('Cover image removed.');
    } catch {
    }
}

async function uploadDescriptionImage(cardId, input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        showToast('Image too large. Max 5MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrfToken);

    try {
        showToast('Uploading image...');

        const res = await fetch(`/cards/${cardId}/description-images`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok) {
            showToast(data.message || 'Upload failed.', 'error');
            return;
        }

        if (data.success) {
            const grid = document.getElementById(`desc-images-${cardId}`);
            if (grid) {
                grid.classList.remove('hidden');

                const div = document.createElement('div');
                div.id = `desc-img-row-${data.image.id}`;
                div.className = 'relative group rounded-xl overflow-hidden';
                div.innerHTML = `
                    <img src="${data.image.url}"
                         alt="Description image"
                         class="w-full h-28 object-cover cursor-pointer rounded-xl
                                hover:opacity-90 transition"
                         onclick="openImageLightbox('${data.image.url}')">
                    <button onclick="removeDescriptionImage(${cardId}, ${data.image.id})"
                            class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50
                                   hover:bg-black/70 text-white rounded-full
                                   flex items-center justify-center text-sm
                                   transition opacity-0 group-hover:opacity-100">
                        &times;
                    </button>`;
                grid.appendChild(div);
            }

            input.value = '';
            cardModalDirty = true;
            showToast('Image uploaded.');
        }
    } catch {
        showToast('Upload failed. Please try again.', 'error');
    }
}

async function removeDescriptionImage(cardId, imageId) {
    const confirmed = await showWarningModal({
        title: 'Remove Image',
        message: 'Remove this image?',
        confirmText: 'Remove Image'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/cards/${cardId}/description-images/${imageId}`, 'DELETE');

        document.getElementById(`desc-img-row-${imageId}`)?.remove();

        const grid = document.getElementById(`desc-images-${cardId}`);
        if (grid && grid.children.length === 0) {
            grid.classList.add('hidden');
        }

        cardModalDirty = true;
        showToast('Image removed.');
    } catch {
    }
}


function openImageLightbox(url) {
    const lightbox = document.getElementById('image-lightbox');
    const img = document.getElementById('lightbox-img');

    if (!lightbox || !img) return;

    img.src = url;
    lightbox.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('image-lightbox');
    if (lightbox) lightbox.classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});    



async function uploadCoverImage(cardId, input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        showToast('Image too large. Max 5MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrfToken);

    try {
        showToast('Uploading cover...');

        const res = await fetch(`/cards/${cardId}/cover-image`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok) {
            showToast(data.message || 'Upload failed.', 'error');
            return;
        }

        if (data.success) {
            const preview = document.getElementById(`cover-image-preview-${cardId}`);
            const img = document.getElementById(`cover-img-${cardId}`);

            if (preview) {
                if (img) img.src = data.cover_image_url;
                preview.classList.remove('hidden');
            }

            const tile = document.getElementById(`card-${cardId}`);
            if (tile) {
                tile.querySelector('.cover-strip')?.remove();

                let coverImg = tile.querySelector('.tile-cover-img');
                if (!coverImg) {
                    coverImg = document.createElement('img');
                    coverImg.className = 'tile-cover-img w-full h-24 object-cover '
                        + 'rounded-t-lg cursor-pointer';
                    coverImg.onclick = () => openCardModal(cardId);
                    tile.prepend(coverImg);
                }
                coverImg.src = data.cover_image_url;
            }

            input.value = '';
            cardModalDirty = true;
            showToast('Cover image set.');
        }
    } catch {
        showToast('Upload failed. Please try again.', 'error');
    }
}

async function removeCoverImage(cardId) {
    try {
        await fetchJSON(`/cards/${cardId}/cover-image`, 'DELETE');

        const preview = document.getElementById(`cover-image-preview-${cardId}`);
        if (preview) preview.classList.add('hidden');

        const tile = document.getElementById(`card-${cardId}`);
        tile?.querySelector('.tile-cover-img')?.remove();

        cardModalDirty = true;
        showToast('Cover image removed.');
    } catch {
    }
}


async function uploadDescriptionImage(cardId, input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        showToast('Image too large. Max 5MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrfToken);

    try {
        showToast('Uploading image...');

        const res = await fetch(`/cards/${cardId}/description-images`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok) {
            showToast(data.message || 'Upload failed.', 'error');
            return;
        }

        if (data.success) {
            const grid = document.getElementById(`desc-images-${cardId}`);
            if (grid) {
                grid.classList.remove('hidden');

                const div = document.createElement('div');
                div.id = `desc-img-row-${data.image.id}`;
                div.className = 'relative group rounded-xl overflow-hidden';
                div.innerHTML = `
                    <img src="${data.image.url}"
                         alt="Description image"
                         class="w-full h-28 object-cover cursor-pointer rounded-xl
                                hover:opacity-90 transition"
                         onclick="openImageLightbox('${data.image.url}')">
                    <button onclick="removeDescriptionImage(${cardId}, ${data.image.id})"
                            class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50
                                   hover:bg-black/70 text-white rounded-full
                                   flex items-center justify-center text-sm
                                   transition opacity-0 group-hover:opacity-100">
                        &times;
                    </button>`;
                grid.appendChild(div);
            }

            input.value = '';
            cardModalDirty = true;
            showToast('Image uploaded.');
        }
    } catch {
        showToast('Upload failed. Please try again.', 'error');
    }
}

async function removeDescriptionImage(cardId, imageId) {
    const confirmed = await showWarningModal({
        title: 'Remove Image',
        message: 'Remove this image?',
        confirmText: 'Remove Image'
    });

    if (!confirmed) return;

    try {
        await fetchJSON(`/cards/${cardId}/description-images/${imageId}`, 'DELETE');

        document.getElementById(`desc-img-row-${imageId}`)?.remove();

        const grid = document.getElementById(`desc-images-${cardId}`);
        if (grid && grid.children.length === 0) {
            grid.classList.add('hidden');
        }

        cardModalDirty = true;
        showToast('Image removed.');
    } catch {
    }
}

function openImageLightbox(url) {
    const lightbox = document.getElementById('image-lightbox');
    const img = document.getElementById('lightbox-img');

    if (!lightbox || !img) return;

    img.src = url;
    lightbox.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('image-lightbox');
    if (lightbox) lightbox.classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

async function uploadEditBoardBg(boardId, input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 8 * 1024 * 1024) {
        showBgStatus('File too large. Maximum size is 8MB.', 'error');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrfToken);

    showBgStatus('Uploading background image...');

    try {
        const res  = await fetch(`/boards/${boardId}/background-image`, {
            method:  'POST',
            body:    formData,
            headers: { 'Accept': 'application/json' },
        });

        const data = await res.json();

        if (!res.ok) {
            showBgStatus(data.message || 'Upload failed.', 'error');
            return;
        }

        if (data.success) {
            const url = data.background_image_url;

            const previewWrap = document.getElementById('edit-bg-preview-wrap');
            const previewImg  = document.getElementById('edit-bg-preview-img');
            const uploadZone  = document.getElementById('edit-bg-upload-zone');

            if (previewImg)  previewImg.src = url;
            if (previewWrap) previewWrap.classList.remove('hidden');
            if (uploadZone)  uploadZone.classList.add('hidden');

            updateBoardHeaderBg(url, null);

            showBgStatus('Background image updated!', 'success');
            input.value = '';
        }
    } catch {
        showBgStatus('Upload failed. Please try again.', 'error');
    }
}

async function removeEditBoardBg(boardId) {
    const confirmed = await showWarningModal({
        title: 'Remove Background',
        message: 'Remove the background image?',
        confirmText: 'Remove Background'
    });

    if (!confirmed) return;

    try {
        const res  = await fetch(`/boards/${boardId}/background-image`, {
            method:  'DELETE',
            headers: {
                'Accept':       'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        const data = await res.json();

        if (data.success) {
            const previewWrap = document.getElementById('edit-bg-preview-wrap');
            const uploadZone  = document.getElementById('edit-bg-upload-zone');

            if (previewWrap) previewWrap.classList.add('hidden');
            if (uploadZone)  uploadZone.classList.remove('hidden');

            const colorInput = document.querySelector(
                'input[name="background_color"]:checked'
            );
            const color = colorInput?.value || '#0052CC';
            updateBoardHeaderBg(null, color);

            showBgStatus('Background image removed.', 'success');
        }
    } catch {
        showBgStatus('Failed to remove background.', 'error');
    }
}

function updateBoardHeaderBg(imageUrl, color) {
    const header = document.getElementById('board-header-bar');
    if (!header) return;

    if (imageUrl) {
        header.style.backgroundImage    = `url(${imageUrl})`;
        header.style.backgroundSize     = 'cover';
        header.style.backgroundPosition = 'center';
        header.style.backgroundColor    = '';

        let overlay = header.querySelector('.bg-overlay');
        if (!overlay) {
            overlay           = document.createElement('div');
            overlay.className = 'bg-overlay absolute inset-0 bg-black/40 z-0';
            header.prepend(overlay);
        }
    } else {
        header.style.backgroundImage = '';
        header.style.backgroundColor = color || '#0052CC';

        header.querySelector('.bg-overlay')?.remove();
    }
}

function previewCreateBg(input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 8 * 1024 * 1024) {
        alert('File too large. Maximum 8MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('create-bg-preview');
        const img     = document.getElementById('create-bg-img');

        if (img)     img.src = e.target.result;
        if (preview) preview.classList.remove('hidden');

        const zone = document.getElementById('create-bg-drop-zone');
        if (zone) {
            const span = zone.querySelector('span');
            if (span) span.textContent = 'Image selected — will upload after board is created';
        }
    };
    reader.readAsDataURL(file);
}

function switchBgTab(tab) {
    const colorPanel = document.getElementById('panel-color');
    const imagePanel = document.getElementById('panel-image');
    const colorTab   = document.getElementById('tab-color');
    const imageTab   = document.getElementById('tab-image');

    const activeClass   = ['bg-blue-700', 'text-white'];
    const inactiveClass = [
        'bg-gray-100', 'dark:bg-gray-700',
        'text-gray-600', 'dark:text-gray-300',
        'hover:bg-gray-200', 'dark:hover:bg-gray-600',
    ];

    if (tab === 'color') {
        colorPanel.classList.remove('hidden');
        imagePanel.classList.add('hidden');
        activeClass.forEach(c   => colorTab.classList.add(c));
        inactiveClass.forEach(c => colorTab.classList.remove(c));
        inactiveClass.forEach(c => imageTab.classList.add(c));
        activeClass.forEach(c   => imageTab.classList.remove(c));
    } else {
        colorPanel.classList.add('hidden');
        imagePanel.classList.remove('hidden');
        activeClass.forEach(c   => imageTab.classList.add(c));
        inactiveClass.forEach(c => imageTab.classList.remove(c));
        inactiveClass.forEach(c => colorTab.classList.add(c));
        activeClass.forEach(c   => colorTab.classList.remove(c));
    }
}

function switchEditBgTab(tab) {
    const colorPanel = document.getElementById('edit-panel-color');
    const imagePanel = document.getElementById('edit-panel-image');
    const colorTab   = document.getElementById('edit-tab-color');
    const imageTab   = document.getElementById('edit-tab-image');

    const activeClass   = ['bg-blue-700', 'text-white'];
    const inactiveClass = [
        'bg-gray-100', 'dark:bg-gray-700',
        'text-gray-600', 'dark:text-gray-300',
        'hover:bg-gray-200', 'dark:hover:bg-gray-600',
    ];

    if (tab === 'color') {
        colorPanel.classList.remove('hidden');
        imagePanel.classList.add('hidden');
        activeClass.forEach(c   => colorTab.classList.add(c));
        inactiveClass.forEach(c => colorTab.classList.remove(c));
        inactiveClass.forEach(c => imageTab.classList.add(c));
        activeClass.forEach(c   => imageTab.classList.remove(c));
    } else {
        colorPanel.classList.add('hidden');
        imagePanel.classList.remove('hidden');
        activeClass.forEach(c   => imageTab.classList.add(c));
        inactiveClass.forEach(c => imageTab.classList.remove(c));
        inactiveClass.forEach(c => colorTab.classList.add(c));
        activeClass.forEach(c   => colorTab.classList.remove(c));
    }
}

function showBgStatus(message, type = 'info') {
    const el = document.getElementById('edit-bg-status');
    if (!el) return;
    el.textContent = message;
    el.className   = 'text-xs mt-2 '
        + (type === 'error'   ? 'text-red-500' :
           type === 'success' ? 'text-green-600 dark:text-green-400' :
                                'text-gray-400 dark:text-gray-500');
    el.classList.remove('hidden');
    if (type !== 'info') {
        setTimeout(() => el.classList.add('hidden'), 4000);
    }
}

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

const activeTimers = {};

const timerSeconds = {};

document.addEventListener('DOMContentLoaded', function () {
    resumeActiveTimers();
});

async function resumeActiveTimers() {

    const cardTiles = document.querySelectorAll('.card-item[data-id]');
    if (!cardTiles.length) return;

    cardTiles.forEach(tile => {
        const cardId       = tile.dataset.id;
        const isRunning    = tile.dataset.isRunning === 'true';
        const elapsed      = parseInt(tile.dataset.elapsed) || 0;

        if (isRunning && cardId) {
            timerSeconds[cardId] = elapsed;
            startTimerInterval(cardId);
            setCardRunningState(cardId, true);
        }
    });
}

async function startTimeTracker(cardId) {
    try {
        const data = await fetchJSON(
            `/cards/${cardId}/time-tracker/start`,
            'POST'
        );

        if (data.success) {
            timerSeconds[cardId] = 0;
            startTimerInterval(cardId);
            setCardRunningState(cardId, true);
            showToast('Timer started. Task in progress.');
            cardModalDirty = true;

        } else {
            const message = data.message || 'Could not start timer.';
            if (data.active_card_title) {
                showToast(
                    `${message} Current task: ${data.active_card_title}`,
                    'error'
                );
            } else {
                showToast(message, 'error');
            }
        }

    } catch {
    }
}


async function stopTimeTracker(cardId) {
    try {
        const data = await fetchJSON(
            `/cards/${cardId}/time-tracker/stop`,
            'POST'
        );

        if (data.success) {
            stopTimerInterval(cardId);
            setCardRunningState(cardId, false);

            updateTotalTimeBadge(
                cardId,
                data.total_formatted,
                data.total_seconds
            );

            showToast(
                `Time logged: ${data.duration_formatted}`
            );

            cardModalDirty = true;

        } else {
            showToast(data.message || 'Could not stop timer.', 'error');
        }

    } catch {
    }
}

function startTimerInterval(cardId) {
    stopTimerInterval(cardId);

    activeTimers[cardId] = setInterval(() => {
        timerSeconds[cardId] = (timerSeconds[cardId] || 0) + 1;
        const formatted = formatTimerDisplay(timerSeconds[cardId]);

        const tileCount = document.getElementById(
            `timer-count-${cardId}`
        );
        if (tileCount) tileCount.textContent = formatted;

        const modalCount = document.getElementById(
            `modal-timer-count-${cardId}`
        );
        if (modalCount) modalCount.textContent = formatted;

    }, 1000);
}

function stopTimerInterval(cardId) {
    if (activeTimers[cardId]) {
        clearInterval(activeTimers[cardId]);
        delete activeTimers[cardId];
    }
    delete timerSeconds[cardId];
}

function formatTimerDisplay(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;

    const hh = String(h).padStart(2, '0');
    const mm = String(m).padStart(2, '0');
    const ss = String(s).padStart(2, '0');

    return `${hh}:${mm}:${ss}`;
}


function setCardRunningState(cardId, isRunning) {

    const tileBtn     = document.getElementById(`timer-btn-${cardId}`);
    const tileDisplay = document.getElementById(`timer-display-${cardId}`);

    const modalBtn     = document.getElementById(`modal-timer-btn-${cardId}`);
    const modalDisplay = document.getElementById(`modal-timer-display-${cardId}`);

    if (isRunning) {

        if (tileBtn) {
            tileBtn.onclick    = () => stopTimeTracker(cardId);
            tileBtn.innerHTML  = `
                <span class="w-2 h-2 bg-red-500 rounded-sm flex-shrink-0">
                </span>
                End Task`;
            tileBtn.className  = tileBtn.className
                .replace('bg-gray-100', 'bg-red-100')
                .replace('dark:bg-gray-700', 'dark:bg-red-900/30')
                .replace('text-gray-600', 'text-red-600')
                .replace('dark:text-gray-300', 'dark:text-red-400')
                .replace('hover:bg-green-100', '')
                .replace('dark:hover:bg-green-900/30', '')
                .replace('hover:text-green-700', '')
                .replace('dark:hover:text-green-400', '');
        }

        if (tileDisplay) {
            tileDisplay.classList.remove('hidden');
        }

        if (modalBtn) {
            modalBtn.onclick   = () => stopTimeTracker(cardId);
            modalBtn.innerHTML = `
                <span class="w-2.5 h-2.5 bg-white rounded-sm
                             flex-shrink-0"></span>
                End Task`;
            modalBtn.className = 'w-full inline-flex items-center '
                + 'justify-center gap-2 text-sm font-medium bg-red-600 '
                + 'hover:bg-red-700 text-white px-3 py-2.5 '
                + 'rounded-xl transition shadow-sm';
        }

        if (modalDisplay) {
            modalDisplay.classList.remove('hidden');
        }

    } else {

        if (tileBtn) {
            tileBtn.onclick   = () => startTimeTracker(cardId);
            tileBtn.innerHTML = `
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                Start Task`;
            tileBtn.className = 'inline-flex items-center gap-1.5 '
                + 'text-xs font-medium bg-gray-100 dark:bg-gray-700 '
                + 'text-gray-600 dark:text-gray-300 hover:bg-green-100 '
                + 'dark:hover:bg-green-900/30 hover:text-green-700 '
                + 'dark:hover:text-green-400 px-2.5 py-1.5 '
                + 'rounded-lg transition';
        }

        if (tileDisplay) {
            tileDisplay.classList.add('hidden');
            const count = document.getElementById(`timer-count-${cardId}`);
            if (count) count.textContent = '00:00:00';
        }

        if (modalBtn) {
            modalBtn.onclick   = () => startTimeTracker(cardId);
            modalBtn.innerHTML = `
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                Start Task`;
            modalBtn.className = 'w-full inline-flex items-center '
                + 'justify-center gap-2 text-sm font-medium bg-green-600 '
                + 'hover:bg-green-700 text-white px-3 py-2.5 '
                + 'rounded-xl transition shadow-sm';
        }

        if (modalDisplay) {
            modalDisplay.classList.add('hidden');
            const count = document.getElementById(`modal-timer-count-${cardId}`);
            if (count) count.textContent = '00:00:00';
        }
    }
}

function updateTotalTimeBadge(cardId, formatted, totalSeconds) {
    const badge = document.getElementById(`total-time-badge-${cardId}`);
    const text  = document.getElementById(`total-time-text-${cardId}`);

    if (text) text.textContent = formatted;

    if (badge && totalSeconds > 0) {
        badge.classList.remove('hidden');
    }

    const modalTotal = document.getElementById(
        `modal-total-time-${cardId}`
    );

    if (modalTotal) {
        modalTotal.textContent = formatted;
        modalTotal.className   = modalTotal.className
            .replace('text-gray-400', 'text-purple-600')
            .replace('dark:text-gray-500', 'dark:text-purple-400');
    }
}

function markCardCompleteInUI(cardId) {
    const tile   = document.getElementById(`card-${cardId}`);
    const circle = document.getElementById(`complete-circle-${cardId}`);
    const tick   = document.getElementById(`complete-tick-${cardId}`);
    const title  = tile?.querySelector('p.text-sm.font-medium');

    if (circle) {
        circle.classList.remove(
            'bg-white/80', 'dark:bg-gray-700/80',
            'border-gray-400', 'dark:border-gray-500',
            'hover:border-green-400'
        );
        circle.classList.add('bg-green-500', 'border-green-500');
    }

    if (tick) {
        tick.classList.remove('hidden');
    }

    if (title) {
        title.classList.add(
            'line-through',
            'text-gray-400',
            'dark:text-gray-500'
        );
        title.classList.remove(
            'text-gray-800',
            'dark:text-gray-100'
        );
    }

    if (tile) {
        tile.setAttribute('data-completed', 'true');
        tile.setAttribute('data-is-running', 'false');
    }

    const tileBtn  = document.getElementById(`timer-btn-${cardId}`);
    const modalBtn = document.getElementById(`modal-timer-btn-${cardId}`);

    if (tileBtn) {
        tileBtn.closest('div')?.remove();
    }

    if (modalBtn) {
        modalBtn.outerHTML = `
            <div class="flex items-center gap-2 bg-gray-50
                        dark:bg-gray-700/50 rounded-xl px-3 py-2.5">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Task completed
                </span>
            </div>`;
    }

    applyBoardFilters();
}