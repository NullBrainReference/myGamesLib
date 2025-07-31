{{-- filepath: resources/views/profile/comments.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Comments by {{ $user->name }}</h3>
    @if($comments->count())
        <ul class="list-group mb-3">
            @foreach($comments as $comment)
                <li class="list-group-item">
                    <strong>
                        <a href="{{ route('game.view', $comment->game->game_id) }}">
                            {{ $comment->game->title }}
                        </a>
                    </strong>
                    <div>{{ $comment->content }}</div>
                    <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>

                    @auth
                        @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    @endauth
                </li>
            @endforeach
        </ul>
        {{ $comments->links('pagination::simple-bootstrap-5') }}
    @else
        <p>No comments yet.</p>
    @endif
</div>
@endsection