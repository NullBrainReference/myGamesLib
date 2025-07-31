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
                </li>
            @endforeach
        </ul>
        {{ $comments->links('pagination::simple-bootstrap-5') }}
    @else
        <p>No comments yet.</p>
    @endif
</div>
@endsection