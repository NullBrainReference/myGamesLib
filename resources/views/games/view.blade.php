@extends('layouts.app')

@section('title', $game->title)

@section('content')

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger text-center">
        {{ session('error') }}
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-md-8 mt-3 w-25">
        <div class="card border rounded p-3">
            <img src="{{ $game->img_src }}" class="card-img-top" alt="{{ $game->title }}">
            <div class="card-body">
                <h5 class="card-title">{{ $game->title }}</h5>
                <p class="card-text">{{ $game->description }}</p>

                @auth
                @if(Auth::user()->games->contains($game->game_id)) 
                    <span class="badge bg-success">In Your Library</span>
                @else
                    <form action="{{ route('library.add', ['id' => $game->game_id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Add to Library</button>
                    </form>
                @endif
                @if(Auth::user()->isAdmin()) 
                    <a href="{{ route('games.edit', ['id' => $game->game_id]) }}" 
                        class="btn btn-warning mt-1">
                        Edit Game
                    </a>

                    <form action="{{ route('games.remove', ['id' => $game->game_id]) }}" method="GET"
                        class="mt-1">
                        <button type="submit" class="btn btn-danger">Delete Game</button>
                    </form>
                    
                @endif
                @endauth
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <h5>Comments</h5>

        @auth
        <form action="{{ route('comments.store', ['id' => $game->game_id]) }}" method="POST" class="mb-3">
            @csrf
            <div class="mb-2">
                <textarea name="content" class="form-control" rows="3" placeholder="Your comment..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
        @endauth

        @forelse ($game->comments as $comment)
            <div class="card mb-2">
                <div class="card-body">
                    <strong>{{ $comment->user->name }}</strong>
                    <p class="mb-1">{{ $comment->content }}</p>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>

                    @auth
                        @if(Auth::id() === $comment->user_id || Auth::user()->isAdmin())
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        @empty
            <p class="text-muted">No comments. Be the first one!</p>
        @endforelse
    </div>
</div>
@endsection
