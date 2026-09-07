@php
    $canCreateProject = $canCreateProject ?? true;
    $userVote = $comment->user_vote;
    $score = $comment->score;
@endphp

<div class="bg-white rounded-lg border border-gray-200 p-4 mb-4 shadow-sm transition-all {{ $comment->parent_id ? 'ml-6 md:ml-12 border-l-4 border-l-blue-500' : '' }}">
    <div class="flex gap-3">
        
        <div class="flex flex-col items-center justify-start pt-1">
            @auth
                <form action="{{ route('comments.vote', $comment->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="up">
                    <button type="submit" 
                            title="Vote Up"
                            class="p-1 rounded hover:bg-gray-100 transition-colors {{ $userVote === 1 ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>
                </form>
            @else
                <span class="text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </span>
            @endauth

            <span class="text-xs font-bold my-0.5 {{ $score > 0 ? 'text-emerald-600' : ($score < 0 ? 'text-red-500' : 'text-gray-500') }}">
                {{ $score > 0 ? '+'.$score : $score }}
            </span>

            @auth
                <form action="{{ route('comments.vote', $comment->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="down">
                    <button type="submit" 
                            title="Vote Down"
                            class="p-1 rounded hover:bg-gray-100 transition-colors {{ $userVote === -1 ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </form>
            @else
                <span class="text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            @endauth
        </div>

        <div class="flex-grow min-w-0">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.view', $comment->user->id) }}" class="text-gray-900 font-bold hover:underline text-sm">
                        {{ $comment->user->name }}
                    </a>
                    
                    @if($comment->parent_id)
                        <span class="text-gray-400 text-xs">
                            replied to <span class="font-medium text-gray-600">{{ $comment->parent->user->name }}</span>
                        </span>
                    @endif
                </div>

                @if($comment->mechanic)
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $comment->mechanic->approved ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                        <i class="bi bi-gear-wide-connected"></i>
                        {{ $comment->mechanic->approved ? '✓ Mechanic Approved' : '⏳ Mechanic Under Review' }}
                    </span>
                @endif
            </div>

            <p class="text-gray-700 mb-3 text-sm leading-relaxed whitespace-pre-line">{{ $comment->content }}</p>

            @if($comment->mechanic)
                <div class="my-3 p-3 bg-amber-50/60 border border-amber-200 rounded-lg">
                    <h5 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Proposed Mechanic: {{ $comment->mechanic->title }}
                    </h5>
                    <p class="text-xs text-gray-700 leading-relaxed">
                        {{ $comment->mechanic->content }}
                    </p>
                </div>
            @endif

            @if($comment->project)
                <div class="my-3 border border-blue-200 rounded-lg bg-blue-50/40 overflow-hidden shadow-sm">
                    <div class="p-3 bg-blue-100/50 flex items-center justify-between gap-2 border-b border-blue-200">
                        <div class="flex items-center gap-2 min-w-0">
                            @if($comment->project->icon_small)
                                <img src="{{ asset('storage/' . $comment->project->icon_small) }}" alt="Icon" class="w-6 h-6 rounded object-cover border flex-shrink-0">
                            @else
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            @endif
                            <h4 class="text-sm font-bold text-blue-900 truncate">
                                {{ $comment->project->title }}
                            </h4>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded {{ $comment->project->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                {{ $comment->project->is_public ? 'Public' : 'Private' }}
                            </span>
                            
                            <button type="button" 
                                    onclick="toggleProjectDetails({{ $comment->project->id }})"
                                    class="text-xs font-semibold text-blue-700 hover:text-blue-900 bg-white border border-blue-300 px-2 py-1 rounded shadow-sm hover:bg-blue-50 transition-colors flex items-center gap-1">
                                <span id="project-toggle-text-{{ $comment->project->id }}">Expand</span>
                                <svg id="project-toggle-icon-{{ $comment->project->id }}" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="project-summary-{{ $comment->project->id }}" class="p-3 text-xs text-gray-600">
                        {{ Str::limit(strip_tags($comment->project->content), 120) }}
                    </div>

                    <div id="project-details-{{ $comment->project->id }}" class="hidden p-4 bg-white border-t border-blue-200">
                        @if($comment->project->icon_big)
                            <div class="mb-3 flex justify-center">
                                <img src="{{ asset('storage/' . $comment->project->icon_big) }}" alt="Project Banner" class="max-h-32 rounded border object-cover">
                            </div>
                        @endif

                        <h5 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Project Description:</h5>
                        <div class="text-xs text-gray-800 space-y-2 mb-4 leading-relaxed border-l-2 border-blue-400 pl-3">
                            {!! Parsedown::instance()->text($comment->project->content) !!}
                        </div>

                        @if($comment->project->mechanics && $comment->project->mechanics->where('approved', true)->count() > 0)
                            <div class="mb-4">
                                <h5 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approved Mechanics:
                                </h5>
                                <ul class="list-disc list-inside text-xs text-gray-700 space-y-1 bg-emerald-50/50 p-2.5 rounded border border-emerald-200">
                                    @foreach($comment->project->mechanics->where('approved', true) as $mech)
                                        <li><strong>{{ $mech->title }}</strong> — {{ Str::limit($mech->content, 80) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <a href="{{ route('projects.view', $comment->project->id) }}" class="inline-flex items-center text-xs text-blue-600 font-bold hover:text-blue-800 hover:underline">
                            Go to project page &rarr;
                        </a>
                    </div>

                    <div class="px-3 py-2 bg-gray-50 border-t border-blue-100 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            @auth
                                {{-- <button type="button" 
                                        onclick="openCreateMechanicModal('{{ route('projects.mechanics.store', $comment->project->id) }}', {{ $comment->id }}, 'Propose Mechanic for Project')"
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 bg-amber-500 text-white rounded hover:bg-amber-600 shadow-sm transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Propose Mechanic
                                </button> --}}
                                <button type="button" 
                                        onclick="openCreateMechanicModal('{{ route('forum.comments.mechanic.store', $comment->id) }}', {{ $comment->id }}, 'Propose Mechanic')" 
                                        class="text-xs text-amber-600 hover:text-amber-700 font-medium flex items-center gap-1">
                                    <span>⚙️</span> Propose Mechanic
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-4 text-xs text-gray-400 mt-2">
                <span>{{ $comment->created_at->diffForHumans() }}</span>

                @auth
                    <button class="text-blue-600 hover:text-blue-800 font-medium hover:underline focus:outline-none"
                            type="button"
                            onclick="toggleTailwindReplyForm(event, {{ $comment->id }})">
                        Reply
                    </button>

                    @if($canCreateProject && !$comment->project)
                        <a href="{{ route('projects.create', ['comment_id' => $comment->id]) }}"
                           class="text-emerald-600 hover:text-emerald-800 font-medium hover:underline">
                            + Create Project
                        </a>
                    @endif

                    @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 font-medium hover:underline">Delete</button>
                        </form>
                    @endif
                @endauth
            </div>

            @auth
                <div id="replyForm-{{ $comment->id }}" class="hidden mt-4 bg-gray-50 p-4 rounded-md border border-gray-200">
                    <form action="{{ route('comments.store', ['type' => $type, 'id' => $object->getKey()]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                        <div class="mb-3">
                            <textarea name="content"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 text-sm"
                                      rows="2"
                                      placeholder="Write a reply for {{ $comment->user->name }}..."
                                      required></textarea>
                        </div>
                        <div class="flex justify-end gap-2 text-xs">
                            <button type="button"
                                    class="px-3 py-1.5 bg-gray-200 text-gray-700 font-medium rounded hover:bg-gray-300"
                                    onclick="toggleTailwindReplyForm(event, {{ $comment->id }})">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-3 py-1.5 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 shadow-sm">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            @endauth

        </div>
    </div>
</div>

@if($comment->replies && $comment->replies->count() > 0)
    <div class="replies-branch mb-4">
        @foreach($comment->replies as $reply)
            @include('partials.comment', [
                'comment' => $reply,
                'type' => $type,
                'object' => $object,
                'canCreateProject' => $canCreateProject
            ])
        @endforeach
    </div>
@endif

@once
<script>
function toggleProjectDetails(projectId) {
    const details = document.getElementById(`project-details-${projectId}`);
    const summary = document.getElementById(`project-summary-${projectId}`);
    const text = document.getElementById(`project-toggle-text-${projectId}`);
    const icon = document.getElementById(`project-toggle-icon-${projectId}`);

    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        summary.classList.add('hidden');
        text.innerText = 'Collapse';
        icon.classList.add('rotate-180');
    } else {
        details.classList.add('hidden');
        summary.classList.remove('hidden');
        text.innerText = 'Expand';
        icon.classList.remove('rotate-180');
    }
}

function toggleTailwindReplyForm(event, commentId) {
    event.preventDefault();
    const form = document.getElementById(`replyForm-${commentId}`);
    form.classList.toggle('hidden');
}
</script>

@endonce