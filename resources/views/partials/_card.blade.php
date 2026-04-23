{{--
    Single task card partial
    Usage: @include('partials._card', ['card' => $card])
--}}

<div class="card-item bg-white dark:bg-gray-800 rounded-lg shadow-sm
            border border-gray-200 dark:border-gray-600
            cursor-pointer hover:shadow-md hover:border-gray-300
            dark:hover:border-gray-500 transition-all group relative"
    data-id="{{ $card->id }}"
    data-title="{{ strtolower($card->title) }}"
    data-description="{{ strtolower($card->description ?? '') }}"
    data-completed="{{ $card->is_completed ? 'true' : 'false' }}"
    id="card-{{ $card->id }}">

    {{-- ── Completion checkbox (visible on hover) ──────────── --}}
    <div class="absolute top-2 left-2 z-10 opacity-0 group-hover:opacity-100
                transition-opacity duration-150"
        onclick="event.stopPropagation(); toggleCardComplete({{ $card->id }}, this)">
        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                    transition-all cursor-pointer
                    {{ $card->is_completed
                        ? 'bg-green-500 border-green-500'
                        : 'bg-white/80 dark:bg-gray-700/80 border-gray-400
                           dark:border-gray-500 hover:border-green-400' }}"
            id="complete-circle-{{ $card->id }}">
            <svg class="w-3 h-3 text-white {{ $card->is_completed ? '' : 'hidden' }}"
                id="complete-tick-{{ $card->id }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </div>

    {{-- ── Cover image (priority) or color strip ──────────── --}}
    @if($card->cover_image_url)
    <div class="relative w-full rounded-t-lg overflow-hidden cursor-pointer"
        onclick="openCardModal({{ $card->id }})">
        <img src="{{ $card->cover_image_url }}"
            alt="Card cover"
            class="w-full h-24 object-cover">
    </div>
    @elseif($card->cover_color)
    <div class="h-8 rounded-t-lg w-full"
        style="background-color: {{ $card->cover_color }}"
        onclick="openCardModal({{ $card->id }})">
    </div>
    @endif

    {{-- ── Card body ────────────────────────────────────────── --}}
    <div class="p-3" onclick="openCardModal({{ $card->id }})">

        {{-- Completed badge --}}
        @if($card->is_completed)
        <div class="flex items-center gap-1.5 mb-2">
            <span class="inline-flex items-center gap-1 text-xs font-medium
                             text-green-600 dark:text-green-400
                             bg-green-50 dark:bg-green-900/30
                             px-2 py-0.5 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Completed
            </span>
        </div>
        @endif

        {{-- Labels row --}}
        @if($card->labels->isNotEmpty())
        <div class="flex flex-wrap gap-1 mb-2">
            @foreach($card->labels as $label)
            <span class="inline-block h-2 w-8 rounded-full"
                style="background-color: {{ $label->color }}"
                title="{{ $label->name }}">
            </span>
            @endforeach
        </div>
        @endif

        {{-- Card title --}}
        <p class="text-sm font-medium leading-snug mb-3 transition-colors
                  group-hover:text-blue-700 dark:group-hover:text-blue-400
                  {{ $card->is_completed
                      ? 'line-through text-gray-400 dark:text-gray-500'
                      : 'text-gray-800 dark:text-gray-100' }}">
            {{ $card->title }}
        </p>

        {{-- ── Footer row ───────────────────────────────────── --}}
        <div class="flex items-center justify-between gap-2">

            {{-- Left side: due date + comment count + attachment count --}}
            <div class="flex items-center gap-2 flex-wrap">

                {{-- Due date badge --}}
                @if($card->due_date)
                <span class="inline-flex items-center gap-1 text-xs font-medium
                                 px-2 py-0.5 rounded-full
                                 {{ $card->isOverdue()
                                     ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                                     : ($card->isDueSoon()
                                         ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300'
                                         : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400') }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $card->due_date->format('M d') }}
                </span>
                @endif

                {{-- Comment count --}}
                @php
                $commentCount = $card->comments_count ?? $card->comments->count();
                @endphp
                @if($commentCount > 0)
                <span class="inline-flex items-center gap-1 text-xs text-gray-400
                                 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    {{ $commentCount }}
                </span>
                @endif

                {{-- Attachment count --}}
                @php
                $attachCount = $card->attachments_count ?? $card->attachments->count();
                @endphp
                @if($attachCount > 0)
                <span class="inline-flex items-center gap-1 text-xs text-gray-400
                                 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    {{ $attachCount }}
                </span>
                @endif

            </div>

            {{-- Right side: assignee avatars --}}
            @if($card->assignees->isNotEmpty())
            <div class="flex items-center -space-x-1.5 flex-shrink-0">
                @foreach($card->assignees->take(3) as $assignee)
                <div class="w-6 h-6 rounded-full bg-blue-700 ring-2
                                    ring-white dark:ring-gray-800
                                    flex items-center justify-center
                                    text-white text-xs font-bold"
                    title="{{ $assignee->name }}">
                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                </div>
                @endforeach
                @if($card->assignees->count() > 3)
                <div class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600
                                    ring-2 ring-white dark:ring-gray-800
                                    flex items-center justify-center
                                    text-gray-600 dark:text-gray-300 text-xs font-bold">
                    +{{ $card->assignees->count() - 3 }}
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>