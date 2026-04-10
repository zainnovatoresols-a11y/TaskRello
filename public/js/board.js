// ============================================================
// BOARD.JS — Complete board interactivity
// Place at: public/js/board.js
// ============================================================

// ── CSRF token (read from meta tag in app.blade.php) ────────
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ── Central fetch helper ─────────────────────────────────────
// All JSON API calls go through this function.
// Automatically attaches CSRF token and correct headers.
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

// ── Toast notification ────────────────────────────────────────
function showToast(message, type = 'success') {
    // Remove any existing toast
    document.querySelectorAll('.js-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'js-toast fixed bottom-6 right-6 z-[9999] px-4 py-3 rounded-xl '
        + 'text-white text-sm font-medium shadow-xl transition-all duration-300 '
        + (type === 'error' ? 'bg-red-600' : 'bg-gray-900');
    toast.textContent = message;
    document.body.appendChild(toast);

    // Fade out and remove after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// MODAL — Card detail
// ============================================================

async function openCardModal(cardId) {
    cardModalDirty = false;
    const modal = document.getElementById('card-modal');
    const body = document.getElementById('card-modal-body');

    // Show spinner
    body.innerHTML = `
        <div class="flex items-center justify-center py-16">
            <div class="w-8 h-8 border-2 border-blue-600 border-t-transparent
                        rounded-full animate-spin"></div>
        </div>`;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        const response = await fetch(`/cards/${cardId}`, {
            headers: { 'Accept': 'text/html', 'X-CSRF-TOKEN': csrfToken },
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
    document.getElementById('card-modal').classList.add('hidden');
    document.body.style.overflow = '';

    // If anything changed, reload the page to show fresh data
    if (cardModalDirty) {
        cardModalDirty = false;
        window.location.reload();
    }
}

// Close modal when clicking the dark backdrop
document.getElementById('card-modal').addEventListener('mousedown', function (e) {
    if (e.target === document.getElementById('card-modal')) closeCardModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCardModal();
        closeLabelsManager();
    }
});

// ============================================================
// LABELS MANAGER MODAL
// ============================================================

function openLabelsManager() {
    document.getElementById('labels-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLabelsManager() {
    document.getElementById('labels-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('labels-modal').addEventListener('click', function (e) {
    if (e.target === this) closeLabelsManager();
});

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
            // Remove "no labels" message if present
            const noMsg = document.getElementById('no-labels-msg');
            if (noMsg) noMsg.remove();

            // Append new label row to the list
            const list = document.getElementById('labels-list');
            const row = document.createElement('div');
            row.id = `label-row-${data.label.id}`;
            row.className = 'flex items-center justify-between py-1.5 px-3 rounded-lg';
            row.style.backgroundColor = data.label.color + '20';
            row.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full flex-shrink-0"
                          style="background-color:${data.label.color}"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        ${data.label.name}
                    </span>
                </div>`;
            list.appendChild(row);

            nameInput.value = '';
            showToast('Label created.');
        }
    } catch (err) {
        // Error toast already shown by fetchJSON
    }
}

// ============================================================
// CARD — Inline title edit
// ============================================================

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

            // Also update title on the board card tile
            const boardCard = document.querySelector(`[data-id="${cardId}"] p`);
            if (boardCard) boardCard.textContent = newTitle;
            cardModalDirty = true;
            showToast('Title updated.');
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

// ============================================================
// CARD — Save any single field (description, due_date, cover_color)
// ============================================================

async function saveCardField(cardId, field, value) {
    try {
        await fetchJSON(`/cards/${cardId}`, 'PUT', { [field]: value || null });

        cardModalDirty = true;
        if (field === 'description') showToast('Description saved.');
        if (field === 'due_date') showToast('Due date saved.');
        if (field === 'cover_color') {
            showToast('Cover updated.');
            // Update cover strip on board card tile
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
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// CARD — Delete
// ============================================================

async function deleteCard(cardId) {
    if (!confirm('Delete this card permanently? This cannot be undone.')) return;

    try {
        await fetchJSON(`/cards/${cardId}`, 'DELETE');

        // Remove from board
        const tile = document.getElementById(`card-${cardId}`);
        if (tile) tile.remove();

        closeCardModal();
        showToast('Card deleted.');
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// COMMENTS
// ============================================================

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

            // Remove "no comments" placeholder if present
            const placeholder = list.querySelector('p.italic');
            if (placeholder) placeholder.remove();

            // Build comment HTML
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
        }
    } catch {
        // Error toast shown by fetchJSON
    }
}

async function deleteComment(commentId) {
    if (!confirm('Delete this comment?')) return;

    try {
        await fetchJSON(`/comments/${commentId}`, 'DELETE');
        document.getElementById(`comment-${commentId}`)?.remove();
        cardModalDirty = true;
        showToast('Comment deleted.');
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// ASSIGNEES
// ============================================================

async function assignUser(cardId, userId) {
    if (!userId) return;

    try {
        const data = await fetchJSON(`/cards/${cardId}/assign`, 'POST', {
            user_id: parseInt(userId),
        });

        if (data.success) {
            // Rebuild assignee avatars in the modal sidebar
            const container = document.getElementById(`assignees-${cardId}`);
            if (container) {
                container.innerHTML = data.assignees.map(u => `
                    <div class="w-7 h-7 rounded-full bg-blue-700
                                flex items-center justify-center
                                text-white text-xs font-bold"
                         title="${u.name}"
                         id="assignee-avatar-${u.id}">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>`).join('');
            }

            // Update assignee avatars on the board card tile
            const tileAvatars = document.querySelector(
                `[data-id="${cardId}"] .assignee-avatars`
            );
            if (tileAvatars && data.assignees.length > 0) {
                tileAvatars.innerHTML = data.assignees.slice(0, 3).map(u => `
                    <div class="w-6 h-6 rounded-full bg-blue-700 ring-2
                                ring-white dark:ring-gray-800
                                flex items-center justify-center
                                text-white text-xs font-bold"
                         title="${u.name}">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>`).join('');
            }
            cardModalDirty = true;
            showToast('Member assignment updated.');
        }
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// LABELS — Attach / Detach
// ============================================================

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
        }
    } catch {
        // Error toast shown by fetchJSON
    }
}

async function detachLabel(cardId, labelId) {
    try {
        const data = await fetchJSON(
            `/cards/${cardId}/labels/${labelId}`, 'DELETE'
        );

        if (data.success) {
            rebuildLabelChips(cardId, data.labels);
            cardModalDirty = true;
            showToast('Label removed.');
        }
    } catch {
        // Error toast shown by fetchJSON
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

// ============================================================
// ATTACHMENTS — Upload / Delete
// ============================================================

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
        }
    } catch {
        showToast('Upload failed. Please try again.', 'error');
    }
}

async function deleteAttachment(attId) {
    if (!confirm('Remove this attachment?')) return;

    try {
        await fetchJSON(`/attachments/${attId}`, 'DELETE');
        document.getElementById(`att-${attId}`)?.remove();
        cardModalDirty = true;
        showToast('Attachment removed.');
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// LIST — Show / Hide add form
// ============================================================

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

// ============================================================
// LIST — Create
// ============================================================

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

            // Init SortableJS on the new cards container
            initCardsSortable(data.list.id);

            hideAddListForm();
            showToast('List added.');
        }
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// LIST — Inline rename (double click)
// ============================================================

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

// ============================================================
// LIST — Archive
// ============================================================

async function archiveList(listId, boardId) {
    if (!confirm('Archive this list? Cards will be preserved.')) return;

    try {
        await fetchJSON(`/boards/${boardId}/lists/${listId}`, 'PUT', {
            is_archived: true,
        });
        document.getElementById(`list-${listId}`)?.remove();
        showToast('List archived.');
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// LIST — Delete
// ============================================================

async function deleteList(listId, boardId) {
    if (!confirm('Delete this list and ALL its cards? This cannot be undone.')) return;

    try {
        await fetchJSON(`/boards/${boardId}/lists/${listId}`, 'DELETE');
        document.getElementById(`list-${listId}`)?.remove();
        showToast('List deleted.');
    } catch {
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// CARD — Show / Hide add form
// ============================================================

function showAddCardForm(listId) {
    document.getElementById(`add-card-btn-${listId}`).classList.add('hidden');
    document.getElementById(`add-card-form-${listId}`).classList.remove('hidden');
    setTimeout(() => {
        const textarea = document.getElementById(`new-card-title-${listId}`);
        textarea.value = '';   // ← clear first
        textarea.focus();      // ← then focus
    }, 50);
}

function hideAddCardForm(listId) {
    document.getElementById(`add-card-btn-${listId}`).classList.remove('hidden');
    document.getElementById(`add-card-form-${listId}`).classList.add('hidden');
    document.getElementById(`new-card-title-${listId}`).value = '';
}

// ============================================================
// CARD — Create
// ============================================================

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
            // Build minimal card HTML and append to the list
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
        // Error toast shown by fetchJSON
    }
}

// ============================================================
// SORTABLEJS — Cards drag and drop
// ============================================================

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

            // No change — skip API call
            if (evt.from === evt.to && evt.oldIndex === evt.newIndex) return;

            try {
                await fetchJSON(`/cards/${cardId}/move`, 'POST', {
                    list_id: parseInt(newListId),
                    position: newPos,
                });
            } catch {
                // Revert the DOM move on failure
                evt.from.insertBefore(
                    evt.item,
                    evt.from.children[evt.oldIndex] || null
                );
                showToast('Move failed — reverted.', 'error');
            }
        },
    });
}

// ============================================================
// SORTABLEJS — Lists (columns) drag and drop
// ============================================================

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

// ============================================================
// HELPERS
// ============================================================

// Get board ID from the current URL: /boards/{id}
function getBoardIdFromUrl() {
    return window.location.pathname.split('/')[2];
}

// Safely escape HTML to prevent XSS when injecting user content
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Build a full list column HTML string (used when creating a new list)
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

// ============================================================
// BOOT — Run when DOM is ready
// ============================================================

document.addEventListener('DOMContentLoaded', function () {
    const boardId = getBoardIdFromUrl();

    // Init SortableJS on every existing cards container
    document.querySelectorAll('.cards-container').forEach(function (el) {
        initCardsSortable(el.dataset.listId);
    });

    // Init SortableJS on the board columns
    initListsSortable(boardId);
});