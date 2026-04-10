{{--
    Single list column partial
    Usage: @include('partials._list', ['list' => $list, 'board' => $board])
--}}

<div class="list-column flex-shrink-0 w-72 rounded-xl flex flex-col"
    style="max-height: calc(100vh - 130px);"
    data-id="{{ $list->id }}"
    id="list-{{ $list->id }}">

    {{-- ── List header ──────────────────────────────────────── --}}
    <div class="flex items-center justify-between px-3 pt-3 pb-2
                bg-gray-200 dark:bg-gray-700 rounded-t-xl">

        {{-- List title — double click to rename --}}
        <h3 class="list-title font-semibold text-sm text-gray-800 dark:text-gray-100
                   flex-1 mr-2 cursor-pointer truncate"
            title="Double-click to rename"
            ondblclick="inlineEditList({{ $list->id }}, this)">
            {{ $list->name }}
        </h3>

        {{-- Card count badge --}}
        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0">
            {{ $list->cards->count() }}
        </span>

        {{-- List options button --}}
        <div x-data="{ open: false }" class="relative flex-shrink-0">
            <button @click="open = !open"
                class="w-6 h-6 flex items-center justify-center rounded
                           text-gray-500 dark:text-gray-400
                           hover:bg-gray-300 dark:hover:bg-gray-600
                           hover:text-gray-700 dark:hover:text-gray-200 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" />
                </svg>
            </button>

            <div x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 top-8 w-40 bg-white dark:bg-gray-800
                        rounded-xl shadow-lg border border-gray-100
                        dark:border-gray-700 py-1 z-30">

                <button onclick="inlineEditList({{ $list->id }},
                                document.querySelector('#list-{{ $list->id }} .list-title'))"
                    class="w-full text-left px-4 py-2 text-sm
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Rename list
                </button>

                <button onclick="archiveList({{ $list->id }}, {{ $board->id }})"
                    class="w-full text-left px-4 py-2 text-sm
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Archive list
                </button>

                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                <button onclick="deleteList({{ $list->id }}, {{ $board->id }})"
                    class="w-full text-left px-4 py-2 text-sm
                               text-red-600 dark:text-red-400
                               hover:bg-red-50 dark:hover:bg-gray-700 transition">
                    Delete list
                </button>
            </div>
        </div>
    </div>

    {{-- ── Cards container (SortableJS target) ─────────────── --}}
    <div class="cards-container flex-1 overflow-y-auto px-2 py-2 space-y-2
                bg-gray-200 dark:bg-gray-700"
        id="cards-{{ $list->id }}"
        data-list-id="{{ $list->id }}"
        style="min-height: 48px;">

        @foreach($list->cards as $card)
        @include('partials._card', ['card' => $card])
        @endforeach

    </div>

    {{-- ── Add card section ─────────────────────────────────── --}}
    <div class="bg-gray-200 dark:bg-gray-700 rounded-b-xl px-2 pb-2 pt-1">

        {{-- Inline add card form (hidden by default) --}}
<div id="add-card-form-{{ $list->id }}" class="hidden mb-1" onclick="event.stopPropagation()">
    <textarea id="new-card-title-{{ $list->id }}"
        placeholder="Enter a title for this card..."
        rows="3"
        maxlength="255"
        onclick="event.stopPropagation()"
        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg
                     p-2.5 text-sm resize-none
                     bg-white dark:bg-gray-800
                     text-gray-900 dark:text-gray-100
                     placeholder-gray-400
                     focus:outline-none focus:ring-2 focus:ring-blue-500
                     focus:border-transparent mb-2"
        onkeydown="if(event.key==='Enter' && !event.shiftKey){
                             event.preventDefault();
                             storeCard({{ $list->id }});
                         }
                         if(event.key==='Escape'){
                             hideAddCardForm({{ $list->id }});
                         }"></textarea>
    <div class="flex items-center gap-2">
        <button onclick="storeCard({{ $list->id }})"
            class="bg-blue-700 hover:bg-blue-800 text-white text-xs
                       font-medium px-3 py-1.5 rounded-lg transition">
            Add card
        </button>
        <button onclick="hideAddCardForm({{ $list->id }})"
            class="text-gray-500 dark:text-gray-400
                       hover:text-gray-700 dark:hover:text-gray-200
                       text-xl leading-none px-1 transition">
            &times;
        </button>
    </div>
</div>

        {{-- Collapsed add card button --}}
        <button id="add-card-btn-{{ $list->id }}"
            onclick="showAddCardForm({{ $list->id }})"
            class="w-full flex items-center gap-1.5 text-left text-sm
                       text-gray-600 dark:text-gray-400
                       hover:text-gray-800 dark:hover:text-gray-200
                       hover:bg-gray-300 dark:hover:bg-gray-600
                       rounded-lg px-2 py-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add a card
        </button>
    </div>

</div>