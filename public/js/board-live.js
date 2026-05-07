const boardLiveCsrf = document.querySelector('meta[name="csrf-token"]')?.content;

const boardsState = document.getElementById('boards-state');
const boardShowState = document.getElementById('board-show-state');

if (boardsState) {
    const boardsGrid = document.getElementById('boards-grid');
    const boardsEmptyState = document.getElementById('boards-empty-state');
    const searchInput = document.getElementById('board-search');

    function buildBoardTile(board) {
        const imageStyle = board.background_image_url
            ? `background-image: url(${board.background_image_url}); background-size: cover; background-position: center;`
            : `background-color: ${board.background_color}`;

        return `
            <a href="/boards/${board.id}"
                class="board-tile group relative rounded-xl p-5 min-h-[130px] flex flex-col justify-between hover:opacity-90 hover:shadow-lg transition-all shadow-sm overflow-hidden"
                data-board-id="${board.id}"
                data-name="${(board.name || '').toLowerCase()}"
                style="${imageStyle}">
                ${board.background_image_url ? '<div class="absolute inset-0 bg-black/35 rounded-xl"></div>' : ''}
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity rounded-xl"></div>
                <h2 class="relative text-white font-bold text-base leading-snug">${escapeHtml(board.name)}</h2>
                <div class="relative mt-4 space-y-1">
                    <div class="flex items-center -space-x-1.5 mb-2">
                        <div class="w-6 h-6 rounded-full bg-white/30 ring-2 ring-white/50 flex items-center justify-center text-white text-xs font-bold">${escapeHtml((board.owner?.name || '').charAt(0).toUpperCase())}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/80 text-xs">${board.members_count} ${escapeHtml(board.members_count === 1 ? 'member' : 'members')}</span>
                        <span class="text-white/70 text-xs truncate max-w-[90px]">${escapeHtml(board.owner?.name || '')}</span>
                    </div>
                </div>
            </a>`;
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    async function refreshBoardsList() {
        try {
            const res = await fetch('/boards', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': boardLiveCsrf,
                },
            });

            if (!res.ok) {
                return;
            }

            const data = await res.json();
            if (!data.status || !Array.isArray(data.data)) {
                return;
            }

            const remoteBoards = data.data;
            const remoteIds = remoteBoards.map(board => board.id.toString());
            const existingTiles = Array.from(boardsGrid.querySelectorAll('[data-board-id]'));
            const existingIds = existingTiles.map(tile => tile.dataset.boardId);

            const hasDifference = remoteIds.length !== existingIds.length || remoteIds.some(id => !existingIds.includes(id));

            if (hasDifference) {
                boardsGrid.innerHTML = remoteBoards.map(buildBoardTile).join('') + `
                    <a href="/boards/create"
                        class="rounded-xl p-5 min-h-[130px] flex flex-col items-center justify-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 border-2 border-dashed border-gray-300 dark:border-gray-600 group">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-sm font-medium">Create new board</span>
                    </a>`;
            }

            if (remoteBoards.length === 0) {
                boardsGrid.classList.add('hidden');
                if (boardsEmptyState) boardsEmptyState.classList.remove('hidden');
            } else {
                boardsGrid.classList.remove('hidden');
                if (boardsEmptyState) boardsEmptyState.classList.add('hidden');
            }

            const query = searchInput?.value.trim().toLowerCase() || '';
            if (query && searchInput) {
                searchInput.dispatchEvent(new Event('input'));
            }
        } catch (err) {
            console.error('Failed to refresh board list:', err);
        }
    }

    refreshBoardsList();
    setInterval(refreshBoardsList, 5000);
}

if (boardShowState) {
    const boardId = boardShowState.dataset.boardId;

    async function refreshBoardAccess() {
        try {
            const res = await fetch(`/boards/${boardId}/state`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': boardLiveCsrf,
                },
            });

            if (!res.ok) {
                window.location.href = '/boards';
                return;
            }
        } catch (err) {
            console.error('Failed to refresh board access:', err);
        }
    }

    refreshBoardAccess();
    setInterval(refreshBoardAccess, 5000);
}
