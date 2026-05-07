@extends('layouts.app')
@section('title', 'Settings — ' . $board->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Back link --}}
    <a href="{{ route('boards.show', $board) }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
              hover:text-gray-700 dark:hover:text-gray-200 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to board
    </a>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">
        Board settings
    </h1>

    {{-- ── Section 1: General settings ─────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">
            General
        </h2>

        <form method="POST" action="{{ route('boards.update', $board) }}">
            @csrf
            @method('PUT')

            {{-- Board name --}}
            <div class="mb-5">
                <label for="name"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Board name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $board->name) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              transition
                              {{ $errors->has('name')
                                  ? 'border-red-400 dark:border-red-500'
                                  : 'border-gray-300 dark:border-gray-600' }}">
                @error('name')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Description
                    <span class="text-gray-400 font-normal ml-1">(optional)</span>
                </label>
                <textarea id="description"
                    name="description"
                    rows="3"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5
                                 text-sm bg-white dark:bg-gray-900
                                 text-gray-900 dark:text-gray-100
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                 resize-none transition">{{ old('description', $board->description) }}</textarea>
            </div>

            {{-- Background ──────────────────────────────────────────── --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Background
                </label>

                {{-- Tab switcher --}}
                <div class="flex gap-2 mb-4">
                    <button type="button"
                        id="edit-tab-color"
                        onclick="switchEditBgTab('color')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition
                       {{ !$board->background_image
                           ? 'bg-blue-700 text-white'
                           : 'bg-gray-100 dark:bg-gray-700 text-gray-600
                              dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Color
                    </button>
                    <button type="button"
                        id="edit-tab-image"
                        onclick="switchEditBgTab('image')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition
                       {{ $board->background_image
                           ? 'bg-blue-700 text-white'
                           : 'bg-gray-100 dark:bg-gray-700 text-gray-600
                              dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Image
                    </button>
                </div>

                {{-- Color picker panel --}}
                <div id="edit-panel-color"
                    class="{{ $board->background_image ? 'hidden' : '' }}">

                    @php
                    $colors = [
                    '#0052CC' => 'Blue',
                    '#00875A' => 'Green',
                    '#FF5630' => 'Red',
                    '#6554C0' => 'Purple',
                    '#FF8B00' => 'Orange',
                    '#00B8D9' => 'Cyan',
                    '#36B37E' => 'Teal',
                    '#172B4D' => 'Navy',
                    ];
                    @endphp

                    <div class="flex flex-wrap gap-3 mb-4">
                        @foreach($colors as $hex => $label)
                        <label class="cursor-pointer group" title="{{ $label }}">
                            <input type="radio"
                                name="background_color"
                                value="{{ $hex }}"
                                class="sr-only peer"
                                {{ old('background_color', $board->background_color) === $hex ? 'checked' : '' }}>
                            <span class="block w-10 h-10 rounded-xl transition-all
                                 ring-2 ring-transparent ring-offset-2
                                 ring-offset-white dark:ring-offset-gray-800
                                 peer-checked:ring-gray-500 dark:peer-checked:ring-gray-300
                                 group-hover:scale-110"
                                style="background-color: {{ $hex }}">
                            </span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Live preview strip --}}
                    <div class="rounded-xl h-12 flex items-center px-4 transition-colors"
                        id="color-preview"
                        style="background-color: {{ old('background_color', $board->background_color) }}">
                        <span class="text-white font-semibold text-sm"
                            id="preview-name">
                            {{ old('name', $board->name) }}
                        </span>
                    </div>
                </div>

                {{-- Image upload panel --}}
                <div id="edit-panel-image"
                    class="{{ $board->background_image ? '' : 'hidden' }}">

                    {{-- Current image preview --}}
                    <div id="edit-bg-preview-wrap"
                        class="{{ $board->background_image ? '' : 'hidden' }} mb-3">
                        <div class="relative rounded-xl overflow-hidden group"
                            style="height: 140px;">
                            <img id="edit-bg-preview-img"
                                src="{{ $board->background_image_url ?? '' }}"
                                alt="Board background"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 rounded-xl"></div>
                            <div class="absolute bottom-2 right-2 flex gap-1.5">
                                <label class="cursor-pointer bg-white/90 hover:bg-white
                                  text-gray-700 text-xs font-medium px-2.5 py-1.5
                                  rounded-lg transition shadow-sm">
                                    Change
                                    <input type="file"
                                        accept="image/*"
                                        class="hidden"
                                        onchange="uploadEditBoardBg({{ $board->id }}, this)">
                                </label>
                                <button type="button"
                                    onclick="removeEditBoardBg({{ $board->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white
                                   text-xs font-medium px-2.5 py-1.5
                                   rounded-lg transition shadow-sm">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Upload zone (shown when no image) --}}
                    <div id="edit-bg-upload-zone"
                        class="{{ $board->background_image ? 'hidden' : '' }}">
                        <label class="flex flex-col items-center justify-center w-full
                          border-2 border-dashed border-gray-300 dark:border-gray-600
                          rounded-xl px-4 py-6 cursor-pointer
                          hover:border-blue-400 dark:hover:border-blue-500
                          hover:bg-blue-50 dark:hover:bg-blue-900/10
                          transition group">
                            <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500
                            transition mb-2"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2
                             l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6
                             20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0
                             00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-500 dark:text-gray-400
                             group-hover:text-blue-600 dark:group-hover:text-blue-400
                             font-medium transition">
                                Click to upload background image
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                JPG, PNG, WEBP or GIF — max 8MB
                            </span>
                            <input type="file"
                                accept="image/*"
                                class="hidden"
                                onchange="uploadEditBoardBg({{ $board->id }}, this)">
                        </label>
                    </div>

                    {{-- Upload progress --}}
                    <p id="edit-bg-status"
                        class="text-xs mt-2 text-gray-400 hidden">
                    </p>
                </div>
            </div>

            <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5
                           rounded-lg text-sm font-medium transition shadow-sm">
                Save changes
            </button>
        </form>
    </div>

    {{-- ── Section 2: Members ────────────────────────────────── --}}
    <div id="board-state" data-board-id="{{ $board->id }}" data-board-owner-id="{{ $board->user_id }}" data-current-user-id="{{ auth()->id() }}"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">
            Members
        </h2>

        {{-- Pending invitations --}}
        <div id="pending-invitations-section" class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-medium text-yellow-700 dark:text-yellow-300 mb-3">
                Pending Invitations
            </h3>
            <div id="pending-invitations-list" class="space-y-3">
                @foreach($board->invitedMembers as $invited)
                <div class="flex items-center justify-between py-2 px-3 rounded-lg
                                bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100
                                dark:border-yellow-900/40">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-yellow-700 flex items-center justify-center
                                        text-white text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($invited->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ $invited->name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $invited->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40
                                dark:text-yellow-300">
                            Pending
                        </span>

                        {{-- Cancel invite (owner only) --}}
                        @if(auth()->id() === $board->user_id)
                        <form method="POST"
                            action="{{ route('boards.members.remove', [$board, $invited]) }}"
                            onsubmit="event.preventDefault(); handleCancelInvite('{{ addslashes($invited->name) }}', this); return false;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs text-red-500 hover:text-red-700
                                                   dark:text-red-400 dark:hover:text-red-300 transition">
                                Cancel
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <p id="pending-invitations-empty" class="text-sm text-gray-500 {{ $board->invitedMembers->count() > 0 ? 'hidden' : '' }}">
                No pending invitations.
            </p>
        </div>

        {{-- Current members list --}}
        <div id="board-members-list" class="space-y-3 mb-6">
            @foreach($board->members as $member)
            <div class="flex items-center justify-between py-2 border-b
                            border-gray-50 dark:border-gray-700 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-700 flex items-center justify-center
                                    text-white text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $member->name }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $member->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Role badge --}}
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            {{ $member->pivot->role === 'owner'
                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ ucfirst($member->pivot->role) }}
                    </span>

                    {{-- Remove member (owner cannot remove self) --}}
                    @if($member->pivot->role !== 'owner' && auth()->id() === $board->user_id)
                    <form method="POST"
                        action="{{ route('boards.members.remove', [$board, $member]) }}"
                        onsubmit="event.preventDefault(); handleRemoveMember('{{ addslashes($member->name) }}', this); return false;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-xs text-red-500 hover:text-red-700
                                               dark:text-red-400 dark:hover:text-red-300 transition">
                            Remove
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Invite member form --}}
        @can('manageMember', $board)
        <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Invite a member
            </h3>
            <form method="POST" action="{{ route('boards.members.add', $board) }}">
                @csrf
                <div class="flex gap-3">
                    <input type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter email address..."
                        class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg
                                      px-3 py-2.5 text-sm bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-gray-100
                                      placeholder-gray-400 dark:placeholder-gray-600
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      focus:border-transparent transition">
                    <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2.5
                                       rounded-lg text-sm font-medium transition flex-shrink-0">
                        Invite
                    </button>
                </div>
                @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </form>
        </div>
        @endcan
    </div>

    @php
    $archivedLists = $board->lists->where('is_archived', true);
    @endphp

    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur rounded-2xl 
            border border-gray-200 dark:border-gray-700 
            shadow-sm p-6 mb-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Archived Lists
            </h2>

            @if($archivedLists->isNotEmpty())
            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                {{ $archivedLists->count() }}
            </span>
            @endif
        </div>

        @if($archivedLists->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <div class="w-12 h-12 mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                📂
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                No archived lists yet
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Archived lists will appear here
            </p>
        </div>
        @else

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($archivedLists as $archivedList)
            <div x-data="{ loading: false }"
                class="group flex items-center justify-between py-4">

                <div class="flex flex-col">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $archivedList->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Archived on {{ $archivedList->updated_at->format('M j, Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST"
                        action="{{ route('lists.update', [$board, $archivedList]) }}"
                        x-data="{
                            loading: false,
                            controller: new AbortController(),
                            async handleUnarchive(e) {
                                e.preventDefault();
                                this.loading = true;
                                try {
                                    const response = await fetch(this.$el.action, {
                                        method: 'POST',
                                        body: new FormData(this.$el),
                                        signal: this.controller.signal
                                    });
                                    if (!response.ok) {
                                        throw new Error('Request failed');
                                    }
                                    window.location.reload();
                                } catch (error) {
                                    if (error.name !== 'AbortError') {
                                        console.error('Unarchive failed:', error);
                                    }
                                    this.loading = false;
                                }
                            }
                        }"
                        @submit="handleUnarchive($event)">

                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_archived" value="0">

                        <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-3 py-1.5 
                               text-xs font-medium rounded-lg
                               bg-green-50 text-green-700 
                               hover:bg-green-100 hover:text-green-800
                               dark:bg-green-900/30 dark:text-green-400 
                               dark:hover:bg-green-900/50
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition">

                            <svg x-show="loading" class="w-4 h-4 animate-spin"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>

                            <span x-show="!loading">Unarchive</span>
                            <span x-show="loading">Restoring...</span>
                        </button>
                    </form>

                    <form method="POST"
                        action="{{ route('lists.destroy', [$board, $archivedList]) }}"
                        x-data="{
                            loading: false,
                            controller: new AbortController(),
                            async handleDelete(e) {
                                e.preventDefault();
                                const name = '{{ addslashes($archivedList->name) }}';
                                const confirmed = await showWarningModal({
                                    title: 'Delete List',
                                    message: `Delete list '${name}'?`,
                                    warningText: 'This cannot be undone.',
                                    confirmText: 'Delete List'
                                });
                                if (!confirmed) {
                                    return;
                                }
                                this.loading = true;
                                try {
                                    const response = await fetch(this.$el.action, {
                                        method: 'POST',
                                        body: new FormData(this.$el),
                                        signal: this.controller.signal
                                    });
                                    if (!response.ok) {
                                        throw new Error('Request failed');
                                    }
                                    window.location.reload();
                                } catch (error) {
                                    if (error.name !== 'AbortError') {
                                        console.error('Delete failed:', error);
                                    }
                                    this.loading = false;
                                }
                            }
                        }"
                        @submit="handleDelete($event)">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-3 py-1.5 
                               text-xs font-medium rounded-lg
                               bg-red-50 text-red-700 
                               hover:bg-red-100 hover:text-red-800
                               dark:bg-red-900/30 dark:text-red-400 
                               dark:hover:bg-red-900/50
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition">

                            <svg x-show="loading" class="w-4 h-4 animate-spin"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>

                            <span x-show="!loading">Delete</span>
                            <span x-show="loading">Deleting...</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @can('delete', $board)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-100
                    dark:border-red-900/50 shadow-sm p-6">

        <h2 class="text-base font-semibold text-red-600 dark:text-red-400 mb-2">
            Danger zone
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Deleting this board is permanent. All lists, cards, comments, and
            attachments will be destroyed and cannot be recovered.
        </p>

        <form method="POST"
            action="{{ route('boards.destroy', $board) }}"
            x-data="{
                loading: false,
                controller: new AbortController(),
                async handleDelete(e) {
                    e.preventDefault();
                    const name = '{{ addslashes($board->name) }}';
                    const confirmed = await showWarningModal({
                        title: 'Delete Board',
                        message: `DELETE board '${name}'?`,
                        warningText: 'This cannot be undone. All lists and cards will be permanently lost.',
                        confirmText: 'Delete Board'
                    });
                    if (!confirmed) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const response = await fetch(this.$el.action, {
                            method: 'POST',
                            body: new FormData(this.$el),
                            signal: this.controller.signal
                        });
                        if (!response.ok) {
                            throw new Error('Request failed');
                        }
                        window.location.href = '{{ route("boards.index") }}';
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Delete failed:', error);
                        }
                        this.loading = false;
                    }
                }
            }"
            @submit="handleDelete($event)">
            @csrf
            @method('DELETE')
            <button type="submit"
                :disabled="loading"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5
                               rounded-lg text-sm font-medium transition
                               disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!loading">Delete this board permanently</span>
                <span x-show="loading">Deleting...</span>
            </button>
        </form>
    </div>
    @endcan

</div>
@endsection

@section('scripts')
<script src="{{ asset('js/board-state.js') }}"></script>
<script src="{{ asset('js/board.js') }}"></script>
<script>
    const preview = document.getElementById('color-preview');
    const previewName = document.getElementById('preview-name');
    const nameInput = document.getElementById('name');

    document.querySelectorAll('input[name="background_color"]').forEach(radio => {
        radio.addEventListener('change', () => {
            preview.style.backgroundColor = radio.value;
        });
    });

    nameInput.addEventListener('input', () => {
        previewName.textContent = nameInput.value || 'Board preview';
    });

    // Warning Modal Functions
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

    async function handleCancelInvite(name, form) {
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

    async function handleRemoveMember(name, form) {
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
</script>
@endsection