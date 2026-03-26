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
                    <form action="{{ route('library.remove', $game->game_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить из библиотеки">
                            Delete from my Library
                        </button>
                    </form>
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

                    <form action="{{ route('games.remove', ['id' => $game->game_id]) }}" method="GET" class="mt-1">
                            <input type="hidden" name="back_url" value="{{ $backUrl }}">
                            <button type="submit" class="btn btn-danger">Delete Game</button>
                        </form>
                @endif
                @endauth
            </div>
        </div>
    </div>
</div>
{{-- Ratings Section --}}
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="text-muted">Average Rating:
                    <div class="star-rating d-inline-block">
                        <div class="back-stars">★★★★★★★★★★</div>
                        <div class="front-stars" style="width: {{ ($game->average_rating / 10) * 100 }}%">★★★★★★★★★★</div>
                    </div>
                    ({{ $game->average_rating }}/10, {{ $game->ratings->count() }} ratings)
                </h5>

                @if(Auth::check())
                    @php $userRating = $game->ratings->where('user_id', Auth::id())->first(); @endphp
                    <hr class="my-3">
                    <form action="{{ route('rating.store', $game->game_id) }}" method="POST" class="d-inline">
                        @csrf
                        <label for="rating" class="me-2 text-muted">Your Rating (1-10):</label>
                        <select name="rating" id="rating" class="form-select form-select-sm d-inline w-auto me-2" required>
                            <option value="">Select</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ ($userRating->rating ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Rate</button>
                    </form>
                @else
                    <p class="text-muted mt-2"><a href="{{ route('login') }}" class="text-decoration-none">Log in to rate</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center mt-4">
    <div class="col-md-8">

        <x-comment-section :object="$game" type="game" :comments="$comments" />

    </div>
</div>
@endsection
