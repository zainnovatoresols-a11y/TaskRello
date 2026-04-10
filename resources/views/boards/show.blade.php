@extends('layouts.app')
@section('title', $board->name)

@section('content')

{{-- ── Board header bar ─────────────────────────────────────── --}}
<div class="px-6 py-3 flex items-center gap-4 flex-wrap"
    style="background-color: {{ $board->background_color }}">

    {{-- Board name --}}
    <h1 class="text-white font-bold text-lg tracking-tight">
        {{ $board->name }}
    </h1>

    {{-- Separator --}}
    <span class="text-white/30 hidden sm:block">|</span>

    {{-- Member avatars --}}
    <div class="flex items-center -space-x-1.5">
        @foreach($board->members->take(5) as $member)
        <div class="w-7 h-7 rounded-full bg-white/30 ring-2 ring-white/40
                        flex items-center justify-center text-white text-xs font-bold"
            title="{{ $member->name }}">
            {{ strtoupper(substr($member->name, 0, 1)) }}
        </div>
        @endforeach
        @if($board->members->count() > 5)
        <div class="w-7 h-7 rounded-full bg-black/20 ring-2 ring-white/40
                        flex items-center justify-center text-white text-xs font-bold">
            +{{ $board->members->count() - 5 }}
        </div>
        @endif
    </div>

    {{-- Right side actions --}}
    <div class="ml-auto flex items-center gap-3">

        {{-- Board settings (owner/member) --}}
        @can('update', $board)
        <a href="{{ route('boards.edit', $board) }}"
            class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30
                      text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
        </a>
        @endcan

        {{-- Labels manager button --}}
        <button onclick="openLabelsManager()"
            class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30
                       text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Labels
        </button>
    </div>
</div>

{{-- ── Kanban board columns ──────────────────────────────────── --}}
<div class="board-scroll flex gap-3 p-4 overflow-x-auto items-start"
    style="min-height: calc(100vh - 112px);"
    id="board-columns">

    {{-- Render each list column --}}
    @foreach($board->lists as $list)
    @include('partials._list', ['list' => $list, 'board' => $board])
    @endforeach

    {{-- ── Add list form ──────────────────────────────────────── --}}
    <div class="flex-shrink-0 w-72" id="add-list-container">

        {{-- Collapsed button --}}
        <button onclick="showAddListForm()"
            id="add-list-btn"
            class="w-full text-left bg-white/20 hover:bg-white/30 text-white
                       text-sm font-medium rounded-xl px-4 py-3 transition backdrop-blur-sm">
            + Add another list
        </button>

        {{-- Expanded form (hidden by default) --}}
        <div id="add-list-form"
            class="hidden bg-gray-100 dark:bg-gray-700 rounded-xl p-3 shadow-sm">
            <input type="text"
                id="new-list-name"
                placeholder="Enter list name..."
                maxlength="255"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg
                          px-3 py-2 text-sm mb-2
                          bg-white dark:bg-gray-800
                          text-gray-900 dark:text-gray-100
                          placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          focus:border-transparent">
            <div class="flex items-center gap-2">
                <button onclick="storeList({{ $board->id }})"
                    class="bg-blue-700 hover:bg-blue-800 text-white text-xs
                               font-medium px-3 py-1.5 rounded-lg transition">
                    Add list
                </button>
                <button onclick="hideAddListForm()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700
                               dark:hover:text-gray-200 text-xl leading-none px-1 transition">
                    &times;
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ── Card detail modal ─────────────────────────────────────── --}}
<div id="card-modal"
    class="hidden fixed inset-0 z-50 flex items-start justify-center pt-12 px-4 pb-4"
    style="background-color: rgba(0,0,0,0.55);">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl
                max-h-[85vh] overflow-y-auto relative" onclick="event.stopPropagation()">

        {{-- Close button --}}
        <button onclick="closeCardModal()"
            class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center
                       rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500
                       dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600
                       transition text-lg font-bold">
            &times;
        </button>

        {{-- Modal body — filled by JS via fetch /cards/{id} --}}
        <div id="card-modal-body" class="p-6">
            <div class="flex items-center justify-center py-12">
                <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent
                            rounded-full animate-spin"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Labels manager modal ─────────────────────────────────── --}}
<div id="labels-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
    style="background-color: rgba(0,0,0,0.55);">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900 dark:text-white text-base">
                Board labels
            </h3>
            <button onclick="closeLabelsManager()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           text-xl font-bold transition">
                &times;
            </button>
        </div>

        {{-- Existing labels --}}
        <div id="labels-list" class="space-y-2 mb-5">
            @foreach($board->labels as $label)
            <div class="flex items-center justify-between py-1.5 px-3 rounded-lg"
                style="background-color: {{ $label->color }}20"
                id="label-row-{{ $label->id }}">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full flex-shrink-0"
                        style="background-color: {{ $label->color }}"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $label->name }}
                    </span>
                </div>
            </div>
            @endforeach
            @if($board->labels->isEmpty())
            <p class="text-sm text-gray-400 text-center py-3" id="no-labels-msg">
                No labels yet. Create one below.
            </p>
            @endif
        </div>

        {{-- Create label form --}}
        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">
                Create new label
            </p>
            <input type="text"
                id="new-label-name"
                placeholder="Label name e.g. Bug, Feature..."
                maxlength="100"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg
                          px-3 py-2 text-sm mb-3
                          bg-white dark:bg-gray-900
                          text-gray-900 dark:text-gray-100
                          placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          focus:border-transparent">

            {{-- Color swatches for label --}}
            <div class="flex flex-wrap gap-2 mb-3" id="label-color-picker">
                @foreach(['#EB5A46','#F2D600','#61BD4F','#0079BF','#C377E0','#FF9F1A','#00C2E0','#51E898'] as $lc)
                <label class="cursor-pointer">
                    <input type="radio" name="label_color" value="{{ $lc }}"
                        class="sr-only peer"
                        {{ $loop->first ? 'checked' : '' }}>
                    <span class="block w-7 h-7 rounded-full ring-2 ring-transparent
                                     ring-offset-1 peer-checked:ring-gray-500 transition"
                        style="background-color: {{ $lc }}"></span>
                </label>
                @endforeach
            </div>

            <button onclick="createLabel({{ $board->id }})"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white text-sm
                           font-medium py-2 rounded-lg transition">
                Create label
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="{{ asset('js/board.js') }}"></script>
@endsection