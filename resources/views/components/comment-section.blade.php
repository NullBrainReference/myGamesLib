<div class="mt-4">
    <h5>Comments ({{ $object->comments()->count() }})</h5>

    @auth
    <form action="{{ route('comments.store', ['type' => $type, 'id' => $object->getKey()]) }}" method="POST" class="mb-4">
        @csrf
        <textarea name="content" class="form-control" rows="3" placeholder="Leave a comment..." required></textarea>
        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>
    @endauth

    @forelse ($comments as $comment)
        @include('partials.comment', ['comment' => $comment])
    @empty
        <p class="text-muted">No comments yet. Be the first!</p>
    @endforelse

    <div class="mt-3">
        {{ $comments->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
