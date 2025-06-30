@extends('layouts.app')

@section('content')
<div class="container">
    <x-callback-message />

    {{-- <h1>Games</h1> --}}
    <div class="d-flex align-items-baseline gap-2 mb-3">
        <h1 class="mb-0">Games</h1>
        <a href="{{ route('dashboard.users') }}" class="text-decoration-none lh-1 pt-1">to users</a>
        <a href="{{ route('dashboard.comments') }}" class="text-decoration-none lh-1 pt-1">to comments</a>
    </div>

    <form method="GET" action="{{ route('dashboard.games') }}" class="mb-3 d-flex">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search by title">
        <button class="btn btn-primary">Search</button>
    </form>


    <a href="{{ route('games.create') }}" class="btn btn-success mt-0 ms-0 mb-1">Add New Game</a>


    @if($games->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($games as $game)
                    <tr>
                        <td>{{ $game->title }}</td>
                        <td>{{ Str::limit($game->description, 80) }}</td>
                        <td><img src="{{ $game->img_src }}" alt="cover" style="width: 60px;"></td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('game.view', $game->game_id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('games.edit', $game->game_id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="GET" action="{{ route('games.remove', $game->game_id) }}">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $games->withQueryString()->links('pagination::simple-bootstrap-5') }}
    @else
        <p>No games found.</p>
    @endif
</div>
@endsection
