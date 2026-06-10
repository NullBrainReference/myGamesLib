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

    <div class="flex items-center gap-4 text-xs text-gray-400">
        <span>{{ $comment->created_at->diffForHumans() }}</span>

        @auth
            <button class="text-blue-600 hover:text-blue-800 font-medium hover:underline focus:outline-none"
                    type="button"
                    onclick="toggleTailwindReplyForm(event, {{ $comment->id }})">
                Reply
            </button>

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
                'object' => $object
            ])
        @endforeach
    </div>
@endif
