{{--
    Card detail modal content
    Loaded via JS: fetch('/cards/{{ $card->id }}')
Returns HTML that is injected into #card-modal-body
--}}

<style>
    .modal-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        padding-right: 28px !important;
    }
</style>

<div data-card-id="{{ $card->id }}" class="font-sans">

    {{-- ── Card title ───────────────────────────────────────── --}}
    <div class="mb-4 pr-10">
        <h2 id="card-title-display-{{ $card->id }}"
            class="text-lg font-bold text-gray-900 dark:text-white leading-snug
                   cursor-text hover:bg-gray-100 dark:hover:bg-gray-700/50
                   rounded-lg px-2 py-1 -mx-2 transition-colors"
            title="Click to edit title"
            onclick="startEditTitle({{ $card->id }}, this)">
            {{ $card->title }}
        </h2>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 px-2 flex items-center gap-1.5 flex-wrap">
            <span>In list</span>
            <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $card->list->name }}</span>
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <span>by</span>
            <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $card->creator->name }}</span>
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <span>{{ $card->created_at->diffForHumans() }}</span>
        </p>
    </div>

    {{-- ── Divider ──────────────────────────────────────────── --}}
    <div class="border-t border-gray-100 dark:border-gray-700 mb-5"></div>

    {{-- ── Two column layout ────────────────────────────────── --}}
    <div class="flex gap-6">

        {{-- ════ Main column ════ --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- Description --}}
            <div>
                <div class="flex items-center gap-2 mb-2.5">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</h3>
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
                           transition-all">{{ $card->description }}</textarea>
            </div>

            {{-- Comments --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Comments
                        <span class="ml-1 font-normal normal-case text-gray-400">({{ $card->comments->count() }})</span>
                    </h3>
                </div>

                {{-- Existing comments --}}
                <div id="comments-list-{{ $card->id }}" class="space-y-3 mb-4">
                    @forelse($card->comments as $comment)
                    <div class="flex gap-3" id="comment-{{ $comment->id }}">
                        <div class="w-7 h-7 rounded-full bg-blue-600 flex-shrink-0
                                    flex items-center justify-center
                                    text-white text-xs font-bold mt-0.5 ring-2 ring-white dark:ring-gray-800">
                            {{ strtoupper(substr($comment->author->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $comment->author->name }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-xl
                                        px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 leading-relaxed">
                                {{ $comment->body }}
                            </div>
                            @if($comment->user_id === auth()->id())
                            <button onclick="deleteComment({{ $comment->id }})"
                                class="text-xs text-gray-400 hover:text-red-500 dark:hover:text-red-400 mt-1.5 transition-colors">
                                Delete
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic py-2">
                        No comments yet. Be the first to comment.
                    </p>
                    @endforelse
                </div>

                {{-- New comment --}}
                <div class="flex gap-3">
                    <div class="w-7 h-7 rounded-full bg-blue-600 flex-shrink-0
                                flex items-center justify-center text-white text-xs font-bold
                                ring-2 ring-white dark:ring-gray-800 mt-0.5">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <textarea id="new-comment-{{ $card->id }}"
                            placeholder="Write a comment..."
                            rows="2"
                            onkeydown="if(event.key==='Enter' && event.ctrlKey){ postComment({{ $card->id }}); }"
                            class="w-full border border-gray-200 dark:border-gray-600
                                   rounded-xl px-3 py-2.5 text-sm resize-none
                                   bg-gray-50 dark:bg-gray-700/50
                                   text-gray-800 dark:text-gray-100
                                   placeholder-gray-400 dark:placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   focus:border-transparent focus:bg-white dark:focus:bg-gray-700
                                   transition-all mb-2"></textarea>
                        <div class="flex items-center gap-3">
                            <button onclick="postComment({{ $card->id }})"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs
                                       font-semibold px-4 py-1.5 rounded-lg transition-colors">
                                Save
                            </button>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Ctrl+Enter to submit</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity log --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Activity</h3>
                </div>
                <div class="space-y-3">
                    @forelse($card->activityLogs->sortByDesc('created_at')->take(15) as $log)
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700
                                    flex-shrink-0 flex items-center justify-center
                                    text-gray-600 dark:text-gray-300 text-xs font-bold">
                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5">
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">No activity yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ════ Sidebar ════ --}}
        <div class="w-44 flex-shrink-0 space-y-5">

            {{-- Members --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Members
                </h4>
                <div id="assignees-{{ $card->id }}"
                    class="flex items-center -space-x-1.5 mb-2.5 min-h-[28px]">
                    @foreach($card->assignees->take(4) as $assignee)
                    <div class="w-7 h-7 rounded-full bg-blue-600
                                ring-2 ring-white dark:ring-gray-800
                                flex items-center justify-center
                                text-white text-xs font-bold"
                        title="{{ $assignee->name }}"
                        id="assignee-avatar-{{ $assignee->id }}">
                        {{ strtoupper(substr($assignee->name, 0, 1)) }}
                    </div>
                    @endforeach
                    @if($card->assignees->count() > 4)
                    <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-600
                                ring-2 ring-white dark:ring-gray-800
                                flex items-center justify-center
                                text-gray-600 dark:text-gray-300 text-xs font-bold">
                        +{{ $card->assignees->count() - 4 }}
                    </div>
                    @endif
                </div>
                <select onchange="assignUser({{ $card->id }}, this.value); this.selectedIndex=0;"
                    class="modal-select w-full border border-gray-200 dark:border-gray-600 rounded-lg
                           px-2 py-1.5 text-xs bg-white dark:bg-gray-700
                           text-gray-700 dark:text-gray-200
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           focus:border-transparent transition cursor-pointer">
                    <option value="" disabled selected hidden>Assign member...</option>
                    @foreach($card->list->board->members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}{{ $card->assignees->contains($member->id) ? ' ✓' : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            {{-- Due date --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Due date
                </h4>
                <input type="date"
                    id="due-date-{{ $card->id }}"
                    value="{{ $card->due_date?->format('Y-m-d') }}"
                    onchange="saveCardField({{ $card->id }}, 'due_date', this.value)"
                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                           px-2 py-1.5 text-xs bg-white dark:bg-gray-700
                           text-gray-700 dark:text-gray-200
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           focus:border-transparent transition">
                @if($card->due_date)
                <p class="text-xs mt-1.5 font-medium
                          {{ $card->isOverdue() ? 'text-red-500' : ($card->isDueSoon() ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-400') }}">
                    {{ $card->isOverdue() ? '⚠ Overdue' : ($card->isDueSoon() ? '⏰ Due today' : '✓ Upcoming') }}
                </p>
                @endif
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            {{-- Labels --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Labels
                </h4>
                <div id="card-labels-{{ $card->id }}"
                    class="flex flex-wrap gap-1 mb-2.5 min-h-[20px]">
                    @foreach($card->labels as $label)
                    <span class="inline-flex items-center text-xs font-medium
                                 text-white px-2 py-0.5 rounded-full cursor-pointer
                                 hover:opacity-75 transition-opacity"
                        style="background-color: {{ $label->color }}"
                        title="Click to remove"
                        id="card-label-{{ $card->id }}-{{ $label->id }}"
                        onclick="detachLabel({{ $card->id }}, {{ $label->id }})">
                        {{ $label->name }}
                    </span>
                    @endforeach
                </div>
                <select onchange="attachLabel({{ $card->id }}, this.value); this.selectedIndex=0;"
                    class="modal-select w-full border border-gray-200 dark:border-gray-600 rounded-lg
                           px-2 py-1.5 text-xs bg-white dark:bg-gray-700
                           text-gray-700 dark:text-gray-200
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           focus:border-transparent transition cursor-pointer">
                    <option value="" disabled selected hidden>Add label...</option>
                    @foreach($card->list->board->labels as $label)
                    <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            {{-- Cover color + image --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400
               uppercase tracking-wider mb-2">
                    Cover
                </h4>

                {{-- Current cover image preview --}}
                @if($card->cover_image_url)
                <div class="relative mb-3 rounded-xl overflow-hidden group"
                    id="cover-image-preview-{{ $card->id }}">
                    <img src="{{ $card->cover_image_url }}"
                        alt="Cover image"
                        class="w-full h-24 object-cover rounded-xl">
                    <button onclick="removeCoverImage({{ $card->id }})"
                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50
                           hover:bg-black/70 text-white rounded-full
                           flex items-center justify-center text-sm
                           transition opacity-0 group-hover:opacity-100">
                        &times;
                    </button>
                </div>
                @else
                <div id="cover-image-preview-{{ $card->id }}" class="hidden">
                    <div class="relative mb-3 rounded-xl overflow-hidden group">
                        <img src="" alt="Cover image"
                            id="cover-img-{{ $card->id }}"
                            class="w-full h-24 object-cover rounded-xl">
                        <button onclick="removeCoverImage({{ $card->id }})"
                            class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50
                               hover:bg-black/70 text-white rounded-full
                               flex items-center justify-center text-sm
                               transition opacity-0 group-hover:opacity-100">
                            &times;
                        </button>
                    </div>
                </div>
                @endif

                {{-- Upload cover image button --}}
                <label class="flex items-center justify-center gap-1.5 w-full cursor-pointer
                  border border-dashed border-gray-300 dark:border-gray-600
                  rounded-lg px-2 py-2 text-xs text-gray-500 dark:text-gray-400
                  hover:border-blue-400 hover:text-blue-600
                  dark:hover:border-blue-500 dark:hover:text-blue-400
                  transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2
                     2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0
                     00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Upload cover image
                    <input type="file"
                        class="hidden"
                        accept="image/*"
                        onchange="uploadCoverImage({{ $card->id }}, this)">
                </label>

                {{-- Color swatches --}}
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">Or pick a color</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['#EB5A46','#F2D600','#61BD4F','#0079BF','#C377E0','#FF9F1A','none'] as $cc)
                    <button onclick="saveCardField({{ $card->id }}, 'cover_color', '{{ $cc === 'none' ? '' : $cc }}')"
                        class="w-6 h-6 rounded-md border-2 transition
                           {{ $card->cover_color === $cc
                               ? 'border-gray-500 scale-110'
                               : 'border-transparent hover:scale-110' }}"
                        style="{{ $cc !== 'none'
                        ? 'background-color:'.$cc
                        : 'background:repeating-linear-gradient(45deg,#ddd 0,#ddd 2px,#fff 0,#fff 8px)' }}"
                        title="{{ $cc === 'none' ? 'No cover' : $cc }}">
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            {{-- Attachments --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Attachments
                </h4>
                <div id="attachments-{{ $card->id }}" class="space-y-1.5 mb-2">
                    @foreach($card->attachments as $att)
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/60
                                border border-gray-100 dark:border-gray-600
                                rounded-lg px-2.5 py-1.5"
                        id="att-{{ $att->id }}">
                        <a href="{{ $att->url }}" target="_blank"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline truncate max-w-[88px]"
                            title="{{ $att->filename }}">
                            {{ Str::limit($att->filename, 14) }}
                        </a>
                        <button onclick="deleteAttachment({{ $att->id }})"
                            class="text-gray-400 hover:text-red-500 dark:hover:text-red-400
                                   text-base leading-none ml-1 flex-shrink-0 transition-colors">
                            &times;
                        </button>
                    </div>
                    @endforeach
                </div>
                <label class="flex items-center justify-center gap-1.5 w-full cursor-pointer
                              border border-dashed border-gray-300 dark:border-gray-600
                              rounded-lg px-2 py-2 text-xs text-gray-500 dark:text-gray-400
                              hover:border-blue-400 hover:text-blue-600
                              dark:hover:border-blue-500 dark:hover:text-blue-400
                              transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Attach file
                    <input type="file" class="hidden" onchange="uploadAttachment({{ $card->id }}, this)">
                </label>
            </div>

            {{-- Delete card --}}
            @can('delete', $card)
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <button onclick="deleteCard({{ $card->id }})"
                    class="w-full text-xs text-red-500 dark:text-red-400
                           hover:text-white hover:bg-red-500 dark:hover:bg-red-500
                           border border-red-200 dark:border-red-800
                           rounded-lg py-2 transition-all font-medium">
                    Delete card
                </button>
            </div>
            @endcan

        </div>
    </div>
</div>