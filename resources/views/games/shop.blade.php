@extends('layouts.app')

@section('title', 'Games')

@section('content')
    <x-callback-message />

    {{-- Search --}}
    <form method="GET" action="{{ route('shop') }}" class="d-flex mt-3 mb-3 mx-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search games by title">
        <button type="submit" class="btn btn-outline-primary">Search</button>
    </form>

    @auth
        @if(Auth::user()->isAdmin())
            <a href="{{ route('games.create') }}" class="btn btn-success mt-0 ms-3">Add New Game</a>
        @endif
    @endauth
    <div class="mb-3"></div>
    {{-- list here --}}
    @foreach($games as $game)
    <div class="d-flex justify-content-center flex-wrap mb-5">
        <div class="w-25">
            <x-game-card :game="$game" />
        </div>
    </div>
    @endforeach

    <div class="d-flex flex-column align-items-center gap-2">
        {{ $games->links('pagination::simple-bootstrap-5') }}
    </div>
@endsection
