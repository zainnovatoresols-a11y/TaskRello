@extends('layouts.app')
@section('title', 'Create Board')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">

    {{-- Back link --}}
    <a href="{{ route('boards.index') }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
              hover:text-gray-700 dark:hover:text-gray-200 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to boards
    </a>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100
                dark:border-gray-700 p-8">

        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
            Create a new board
        </h1>
        <p class="text-sm text-gray-400 dark:text-gray-500 mb-7">
            Boards are made up of lists and cards. Use them to organise anything.
        </p>

        <form method="POST" action="{{ route('boards.store') }}">
            @csrf

            {{-- Board name --}}
            <div class="mb-5">
                <label for="name"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Board name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="e.g. Sprint 3, Marketing Q2, Personal Tasks..."
                    autofocus
                    class="w-full border rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              placeholder-gray-400 dark:placeholder-gray-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              transition
                              {{ $errors->has('name')
                                  ? 'border-red-400 dark:border-red-500'
                                  : 'border-gray-300 dark:border-gray-600' }}">
                @error('name')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Description
                    <span class="text-gray-400 dark:text-gray-500 font-normal ml-1">(optional)</span>
                </label>
                <textarea id="description"
                    name="description"
                    rows="3"
                    placeholder="What is this board for? Who is it for?"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5
                                 text-sm bg-white dark:bg-gray-900
                                 text-gray-900 dark:text-gray-100
                                 placeholder-gray-400 dark:placeholder-gray-600
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                 resize-none transition">{{ old('description') }}</textarea>
            </div>

            {{-- Background color picker --}}
            <div class="mb-8">
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

                <div class="flex flex-wrap gap-3">
                    @foreach($colors as $hex => $label)
                    <label class="cursor-pointer group" title="{{ $label }}">
                        <input type="radio"
                            name="background_color"
                            value="{{ $hex }}"
                            class="sr-only peer"
                            {{ old('background_color', '#0052CC') === $hex ? 'checked' : '' }}>
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

                @error('background_color')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preview strip --}}
            <div class="mb-8 rounded-xl h-14 flex items-center px-4 transition-colors"
                id="color-preview"
                style="background-color: {{ old('background_color', '#0052CC') }}">
                <span class="text-white font-semibold text-sm opacity-80">
                    {{ old('name') ?: 'Board preview' }}
                </span>
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5
                               rounded-lg text-sm font-medium transition shadow-sm">
                    Create board
                </button>
                <a href="{{ route('boards.index') }}"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700
                          dark:hover:text-gray-200 px-5 py-2.5 rounded-lg text-sm
                          hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live preview — update strip color and name as user types/selects
    const preview = document.getElementById('color-preview');
    const nameInput = document.getElementById('name');

    document.querySelectorAll('input[name="background_color"]').forEach(radio => {
        radio.addEventListener('change', () => {
            preview.style.backgroundColor = radio.value;
        });
    });

    nameInput.addEventListener('input', () => {
        preview.querySelector('span').textContent = nameInput.value || 'Board preview';
    });
</script>
@endsection