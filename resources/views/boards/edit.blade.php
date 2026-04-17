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

            {{-- Background color --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Background color
                </label>

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
                    <span class="text-white font-semibold text-sm" id="preview-name">
                        {{ old('name', $board->name) }}
                    </span>
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
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">
            Members
        </h2>

        {{-- Current members list --}}
        <div class="space-y-3 mb-6">
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
                        onsubmit="return confirm('Remove {{ $member->name }} from this board?')">
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

    {{-- ── Section 3: Danger zone ────────────────────────────── --}}
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
            onsubmit="return confirm('DELETE board \'{{ addslashes($board->name) }}\'?\n\nThis cannot be undone. All lists and cards will be permanently lost.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5
                               rounded-lg text-sm font-medium transition">
                Delete this board permanently
            </button>
        </form>
    </div>
    @endcan

</div>
@endsection

@section('scripts')
<script>
    // Live color preview on edit page
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
</script>
@endsection