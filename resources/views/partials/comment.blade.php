<div class="card mb-2 {{ $comment->parent_id ? 'ms-4 border-start border-primary border-2 shadow-sm' : '' }}">
    <div class="card-body">
        <strong>
            <a href="{{ route('profile.view', $comment->user->id) }}">
                {{ $comment->user->name }}
            </a>
        </strong>
        @if($comment->parent_id)
            <span class="text-muted small">replied to {{ $comment->parent->user->name }}</span>
        @endif

        <p class="mb-1 text-secondary">{{ $comment->content }}</p>
        <small class="text-muted d-block mb-2">{{ $comment->created_at->diffForHumans() }}</small>

        <div class="d-flex gap-2 align-items-center">
            @auth
                <button class="btn btn-sm btn-link text-decoration-none p-0"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#replyForm-{{ $comment->id }}">
                    Reply
                </button>

                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-link text-danger text-decoration-none p-0 m-0">Delete</button>
                    </form>
                @endif
            @endauth
        </div>

        @auth
            <div class="collapse mt-3" id="replyForm-{{ $comment->id }}">
                <form action="{{ route('comments.store', ['type' => $type, 'id' => $object->getKey()]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <div class="input-group">
                        <textarea name="content" class="form-control form-control-sm" rows="2" placeholder="Write a reply..." required></textarea>
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        @endauth
    </div>
</div>

@if($comment->replies->count() > 0)
    <div class="replies-wrapper">
        @foreach($comment->replies as $reply)
            @include('partials.comment', ['comment' => $reply])
        @endforeach
    </div>
@endif
