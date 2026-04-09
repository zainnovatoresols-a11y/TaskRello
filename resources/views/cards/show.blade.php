{{--
    Card detail modal content
    Loaded via JS: fetch('/cards/{{ $card->id }}')
Returns HTML that is injected into #card-modal-body
--}}

<div data-card-id="{{ $card->id }}">

    {{-- ── Card title (inline editable) ───────────────────── --}}
    <div class="mb-1 pr-8">
        <h2 id="card-title-display-{{ $card->id }}"
            class="text-xl font-bold text-gray-900 dark:text-white leading-snug
                   cursor-text hover:bg-gray-50 dark:hover:bg-gray-700/50
                   rounded-lg px-2 py-1 -mx-2 transition-colors"
            title="Click to edit title"
            onclick="startEditTitle({{ $card->id }}, this)">
            {{ $card->title }}
        </h2>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 px-2">
            In list
            <span class="font-medium text-gray-600 dark:text-gray-300">
                {{ $card->list->name }}
            </span>
            &mdash; created by
            <span class="font-medium text-gray-600 dark:text-gray-300">
                {{ $card->creator->name }}
            </span>
            <span class="ml-1">{{ $card->created_at->diffForHumans() }}</span>
        </p>
    </div>

    {{-- ── Two column layout ────────────────────────────────── --}}
    <div class="flex gap-6 mt-5">

        {{-- ════ Main column (left, 2/3 width) ════ --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- Description --}}
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Description
                    </h3>
                </div>
                <textarea id="card-description-{{ $card->id }}"
                    placeholder="Add a more detailed description..."
                    rows="4"
                    onblur="saveCardField({{ $card->id }}, 'description', this.value)"
                    class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                 px-3 py-2.5 text-sm resize-none
                                 bg-gray-50 dark:bg-gray-700/50
                                 text-gray-800 dark:text-gray-100
                                 placeholder-gray-400 dark:placeholder-gray-500
                                 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 focus:border-transparent focus:bg-white dark:focus:bg-gray-700
                                 transition">{{ $card->description }}</textarea>
            </div>

            {{-- ── Comments ──────────────────────────────────── --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Comments
                        <span class="text-gray-400 font-normal ml-1">
                            ({{ $card->comments->count() }})
                        </span>
                    </h3>
                </div>

                {{-- Existing comments list --}}
                <div id="comments-list-{{ $card->id }}" class="space-y-4 mb-4">
                    @forelse($card->comments as $comment)
                    <div class="flex gap-3" id="comment-{{ $comment->id }}">

                        {{-- Avatar --}}
                        <div class="w-8 h-8 rounded-full bg-blue-700 flex-shrink-0
                                        flex items-center justify-center
                                        text-white text-xs font-bold mt-0.5">
                            {{ strtoupper(substr($comment->author->name, 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Author + time --}}
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-800
                                                 dark:text-gray-100">
                                    {{ $comment->author->name }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Comment body --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl
                                            px-3 py-2.5 text-sm text-gray-700
                                            dark:text-gray-200 leading-relaxed">
                                {{ $comment->body }}
                            </div>

                            {{-- Delete (author only) --}}
                            @if($comment->user_id === auth()->id())
                            <button onclick="deleteComment({{ $comment->id }})"
                                class="text-xs text-gray-400 dark:text-gray-500
                                                   hover:text-red-500 dark:hover:text-red-400
                                                   mt-1 transition">
                                Delete
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                        No comments yet. Be the first to comment.
                    </p>
                    @endforelse
                </div>

                {{-- New comment form --}}
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-700 flex-shrink-0
                                flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <textarea id="new-comment-{{ $card->id }}"
                            placeholder="Write a comment..."
                            rows="2"
                            onkeydown="if(event.key==='Enter' && event.ctrlKey){
                                                 postComment({{ $card->id }});
                                             }"
                            class="w-full border border-gray-200 dark:border-gray-600
                                         rounded-xl px-3 py-2.5 text-sm resize-none
                                         bg-gray-50 dark:bg-gray-700/50
                                         text-gray-800 dark:text-gray-100
                                         placeholder-gray-400
                                         focus:outline-none focus:ring-2 focus:ring-blue-500
                                         focus:border-transparent focus:bg-white
                                         dark:focus:bg-gray-700 transition mb-2">
                        </textarea>
                        <button onclick="postComment({{ $card->id }})"
                            class="bg-blue-700 hover:bg-blue-800 text-white text-xs
                                       font-medium px-3 py-1.5 rounded-lg transition">
                            Save
                        </button>
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">
                            Ctrl+Enter to submit
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Activity log ──────────────────────────────── --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Activity
                    </h3>
                </div>

                <div class="space-y-3">
                    @forelse($card->activityLogs->sortByDesc('created_at')->take(15) as $log)
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700
                                        flex-shrink-0 flex items-center justify-center
                                        text-gray-600 dark:text-gray-300 text-xs font-bold">
                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ $log->description }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">
                        No activity yet.
                    </p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ════ Sidebar (right, fixed width) ════ --}}
        <div class="w-44 flex-shrink-0 space-y-5">

            {{-- Members --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400
                           uppercase tracking-wider mb-2">
                    Members
                </h4>

                {{-- Assigned avatars --}}
                <div id="assignees-{{ $card->id }}"
                    class="flex flex-wrap gap-1.5 mb-2 min-h-[28px]">
                    @foreach($card->assignees as $assignee)
                    <div class="w-7 h-7 rounded-full bg-blue-700
                                    flex items-center justify-center
                                    text-white text-xs font-bold"
                        title="{{ $assignee->name }}"
                        id="assignee-avatar-{{ $assignee->id }}">
                        {{ strtoupper(substr($assignee->name, 0, 1)) }}
                    </div>
                    @endforeach
                </div>

                {{-- Assign dropdown --}}
                <select onchange="assignUser({{ $card->id }}, this.value); this.selectedIndex=0;"
                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                               px-2 py-1.5 text-xs
                               bg-white dark:bg-gray-700
                               text-gray-700 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:border-transparent transition cursor-pointer">
                    <option value="">Assign member...</option>
                    @foreach($card->list->board->members as $member)
                    <option value="{{ $member->id }}">
                        {{ $member->name }}
                        {{ $card->assignees->contains($member->id) ? '(assigned)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Due date --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400
                           uppercase tracking-wider mb-2">
                    Due date
                </h4>
                <input type="date"
                    id="due-date-{{ $card->id }}"
                    value="{{ $card->due_date?->format('Y-m-d') }}"
                    onchange="saveCardField({{ $card->id }}, 'due_date', this.value)"
                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                              px-2 py-1.5 text-xs
                              bg-white dark:bg-gray-700
                              text-gray-700 dark:text-gray-200
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              focus:border-transparent transition">
                @if($card->due_date)
                <p class="text-xs mt-1
                              {{ $card->isOverdue()
                                  ? 'text-red-500 dark:text-red-400'
                                  : ($card->isDueSoon()
                                      ? 'text-yellow-600 dark:text-yellow-400'
                                      : 'text-gray-400') }}">
                    {{ $card->isOverdue() ? 'Overdue!' : ($card->isDueSoon() ? 'Due today' : 'Upcoming') }}
                </p>
                @endif
            </div>

            {{-- Labels --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400
                           uppercase tracking-wider mb-2">
                    Labels
                </h4>

                {{-- Attached labels --}}
                <div id="card-labels-{{ $card->id }}"
                    class="flex flex-wrap gap-1 mb-2 min-h-[20px]">
                    @foreach($card->labels as $label)
                    <span class="inline-flex items-center gap-1 text-xs font-medium
                                     text-white px-2 py-0.5 rounded-full cursor-pointer
                                     hover:opacity-80 transition"
                        style="background-color: {{ $label->color }}"
                        title="Click to remove"
                        id="card-label-{{ $card->id }}-{{ $label->id }}"
                        onclick="detachLabel({{ $card->id }}, {{ $label->id }})">
                        {{ $label->name }}
                    </span>
                    @endforeach
                </div>

                {{-- Attach label dropdown --}}
                <select onchange="attachLabel({{ $card->id }}, this.value); this.selectedIndex=0;"
                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                               px-2 py-1.5 text-xs
                               bg-white dark:bg-gray-700
                               text-gray-700 dark:text-gray-200
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:border-transparent transition cursor-pointer">
                    <option value="">Add label...</option>
                    @foreach($card->list->board->labels as $label)
                    <option value="{{ $label->id }}">
                        {{ $label->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Cover color --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400
                           uppercase tracking-wider mb-2">
                    Cover color
                </h4>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['#EB5A46','#F2D600','#61BD4F','#0079BF','#C377E0','#FF9F1A','none'] as $cc)
                    <button onclick="saveCardField({{ $card->id }}, 'cover_color', '{{ $cc === 'none' ? '' : $cc }}')"
                        class="w-6 h-6 rounded-md border-2 transition
                                       {{ $card->cover_color === $cc
                                           ? 'border-gray-500 scale-110'
                                           : 'border-transparent hover:scale-110' }}"
                        style="{{ $cc !== 'none' ? 'background-color:'.$cc : 'background:repeating-linear-gradient(45deg,#ddd 0,#ddd 2px,#fff 0,#fff 8px)' }}"
                        title="{{ $cc === 'none' ? 'No cover' : $cc }}">
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Attachments --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400
                           uppercase tracking-wider mb-2">
                    Attachments
                </h4>

                <div id="attachments-{{ $card->id }}" class="space-y-1.5 mb-2">
                    @foreach($card->attachments as $att)
                    <div class="flex items-center justify-between bg-gray-50
                                    dark:bg-gray-700 rounded-lg px-2 py-1.5 group"
                        id="att-{{ $att->id }}">
                        <a href="{{ $att->url }}"
                            target="_blank"
                            class="text-xs text-blue-600 dark:text-blue-400
                                      hover:underline truncate max-w-[80px]"
                            title="{{ $att->filename }}">
                            {{ Str::limit($att->filename, 14) }}
                        </a>
                        <button onclick="deleteAttachment({{ $att->id }})"
                            class="text-gray-300 dark:text-gray-600
                                           hover:text-red-500 dark:hover:text-red-400
                                           text-base leading-none ml-1 flex-shrink-0
                                           transition">
                            &times;
                        </button>
                    </div>
                    @endforeach
                </div>

                {{-- Upload button --}}
                <label class="flex items-center justify-center gap-1.5 w-full cursor-pointer
                              border border-dashed border-gray-300 dark:border-gray-600
                              rounded-lg px-2 py-2 text-xs text-gray-500 dark:text-gray-400
                              hover:border-blue-400 hover:text-blue-600
                              dark:hover:border-blue-500 dark:hover:text-blue-400
                              transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Attach file
                    <input type="file"
                        class="hidden"
                        onchange="uploadAttachment({{ $card->id }}, this)">
                </label>
            </div>

            {{-- Delete card --}}
            @can('delete', $card)
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                <button onclick="deleteCard({{ $card->id }})"
                    class="w-full text-xs text-red-500 dark:text-red-400
                                   hover:text-red-700 dark:hover:text-red-300
                                   border border-red-200 dark:border-red-800
                                   hover:border-red-400 dark:hover:border-red-600
                                   rounded-lg py-2 transition">
                    Delete card
                </button>
            </div>
            @endcan

        </div>
    </div>
</div>