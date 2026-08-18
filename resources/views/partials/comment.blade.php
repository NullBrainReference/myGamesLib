@php
    $canCreateProject = $canCreateProject ?? true;
@endphp

<div class="bg-white rounded-lg border border-gray-200 p-4 mb-4 shadow-sm {{ $comment->parent_id ? 'ml-6 md:ml-12 border-l-4 border-l-blue-500' : '' }}">

    <div class="flex items-center gap-2 mb-2">
        <strong>
            <a href="{{ route('profile.view', $comment->user->id) }}" class="text-gray-900 font-bold hover:underline">
                {{ $comment->user->name }}
            </a>
        </strong>
        @if($comment->parent_id)
            <span class="text-gray-400 text-xs">
                replied to <span class="font-medium text-gray-600">{{ $comment->parent->user->name }}</span>
            </span>
        @endif
    </div>

    <p class="text-gray-700 mb-3 text-sm leading-relaxed" style="white-space: pre-line;">{{ $comment->content }}</p>

    @if($comment->project)
        <div class="my-3 p-3 bg-blue-50/50 border border-blue-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <h4 class="text-sm font-semibold text-blue-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    {{ $comment->project->title }}
                </h4>
                <span class="text-[10px] font-medium px-2 py-0.5 rounded {{ $comment->project->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                    {{ $comment->project->is_public ? 'Public' : 'Private' }}
                </span>
            </div>
            <p class="text-xs text-gray-600 mb-2">
                {{ Str::limit($comment->project->content, 120) }}
            </p>
            <a href="{{ route('projects.view', $comment->project->id) }}" class="inline-flex items-center text-xs text-blue-600 font-semibold hover:text-blue-800 hover:underline">
                View Project &rarr;
            </a>
        </div>
    @endif

    <div class="flex items-center gap-4 text-xs text-gray-400">
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
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2 text-sm"
                              rows="2"
                              placeholder="Write a reply to {{ $comment->user->name }}..."
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
                        Submit Reply
                    </button>
                </div>
            </form>
        </div>
    @endauth
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
