@extends('layouts.app')
@section('title', 'My Boards')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Boards</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                All boards you own or are a member of
            </p>
        </div>
        <a href="{{ route('boards.create') }}"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New board
        </a>
    </div>


    {{-- ── Search bar ───────────────────────────────────────────── --}}
    <div class="mb-6">
        <div class="relative max-w-sm">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                id="board-search"
                placeholder="Search boards..."
                autocomplete="off"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300
                      dark:border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-sm
                      text-gray-900 dark:text-gray-100 placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                      focus:border-transparent transition">

            {{-- Clear button --}}
            <button id="board-search-clear"
                onclick="clearBoardSearch()"
                class="hidden absolute inset-y-0 right-0 pr-3.5 flex items-center
                       text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- No results message --}}
        <p id="board-no-results"
            class="hidden text-sm text-gray-400 dark:text-gray-500 mt-3 pl-1">
            No boards match your search.
        </p>
    </div>

    {{-- ── Empty state ──────────────────────────────────────── --}}
    @if($boards->isEmpty())
    <div class="flex flex-col items-center justify-center py-28 text-center">
        <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 0a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">
            No boards yet
        </h2>
        <p class="text-gray-400 dark:text-gray-500 text-sm mb-6 max-w-xs">
            Create your first board to start organising your work like a pro
        </p>
        <a href="{{ route('boards.create') }}"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create your first board
        </a>
    </div>

    {{-- ── Boards grid ──────────────────────────────────────── --}}
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

        @foreach($boards as $board)
        <a href="{{ route('boards.show', $board) }}"
            class="group relative rounded-xl p-5 min-h-[130px] flex flex-col
          justify-between hover:opacity-90 hover:shadow-lg transition-all
          shadow-sm overflow-hidden board-tile"
            data-name="{{ strtolower($board->name) }}"
            style="background-color: {{ $board->background_color }}">

            {{-- Subtle overlay on hover --}}
            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity rounded-xl"></div>

            {{-- Board name --}}
            <h2 class="relative text-white font-bold text-base leading-snug">
                {{ $board->name }}
            </h2>

            {{-- Board meta --}}
            <div class="relative mt-4 space-y-1">

                {{-- Member avatars --}}
                <div class="flex items-center -space-x-1.5 mb-2">
                    @foreach($board->members->take(4) as $member)
                    <div class="w-6 h-6 rounded-full bg-white/30 ring-2 ring-white/50
                                            flex items-center justify-center text-white text-xs font-bold"
                        title="{{ $member->name }}">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    @endforeach
                    @if($board->members->count() > 4)
                    <div class="w-6 h-6 rounded-full bg-black/20 ring-2 ring-white/50
                                            flex items-center justify-center text-white text-xs font-bold">
                        +{{ $board->members->count() - 4 }}
                    </div>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-white/80 text-xs">
                        {{ $board->members_count }}
                        {{ Str::plural('member', $board->members_count) }}
                    </span>
                    <span class="text-white/70 text-xs truncate max-w-[90px]">
                        {{ $board->owner->name }}
                    </span>
                </div>
            </div>
        </a>
        @endforeach

        {{-- Create new board shortcut tile --}}
        <a href="{{ route('boards.create') }}"
            class="rounded-xl p-5 min-h-[130px] flex flex-col items-center justify-center gap-2
                      bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600
                      transition text-gray-500 dark:text-gray-400 hover:text-gray-700
                      dark:hover:text-gray-200 border-2 border-dashed border-gray-300
                      dark:border-gray-600 group">
            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 group-hover:text-gray-600
                            dark:group-hover:text-gray-300 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
            </svg>
            <span class="text-sm font-medium">Create new board</span>
        </a>

    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    const boardSearchInput = document.getElementById('board-search');
    const boardClearBtn = document.getElementById('board-search-clear');
    const noResults = document.getElementById('board-no-results');

    boardSearchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        const tiles = document.querySelectorAll('.board-tile');
        let visible = 0;

        tiles.forEach(tile => {
            const name = tile.dataset.name || '';
            const show = name.includes(query);
            tile.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Toggle clear button
        boardClearBtn.classList.toggle('hidden', query === '');

        // Toggle no results message
        noResults.classList.toggle('hidden', visible > 0 || query === '');
    });

    function clearBoardSearch() {
        boardSearchInput.value = '';
        boardSearchInput.dispatchEvent(new Event('input'));
        boardSearchInput.focus();
    }
</script>
@endsection