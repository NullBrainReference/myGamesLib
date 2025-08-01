<div class="mt-4">
    <h5>Comments</h5>

    @auth
    <form action="{{ route('comments.store', ['type' => $type, 'id' => $object->getKey()]) }}" method="POST" class="mb-3">
        @csrf
        <textarea name="content" class="form-control" rows="3" placeholder="Leave a comment..." required></textarea>
        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>
    @endauth

    @forelse ($comments as $comment)
        <div class="card mb-2">
            <div class="card-body">
                <strong>
                    <a href="{{ route('profile.view', $comment->user->id) }}">
                        {{ $comment->user->name }}
                    </a>
                </strong>
                <p class="mb-1">{{ $comment->content }}</p>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>

                @auth
                    @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="mt-1">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <p class="text-muted">No comments yet. Be the first!</p>
    @endforelse

    <div class="mt-3">
        {{ $comments->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
