@extends('layouts.app')

@section('title', $game->title)

@section('content')
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
                    <form action="{{ route('games.add', ['id' => $game->game_id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Add to Library</button>
                    </form>
                @endif
                @if(Auth::user()->isAdmin()) 
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
@endsection
