@extends('layouts.app')

@section('content')
<div class="container">
    <x-callback-message />

    <div class="d-flex align-items-baseline gap-2 mb-3">
        <h1 class="mb-0">Comments</h1>
        <a href="{{ route('dashboard.games') }}" class="text-decoration-none lh-1 pt-1">to games</a>
        <a href="{{ route('dashboard.users') }}" class="text-decoration-none lh-1 pt-1">to users</a>
    </div>

    {{-- <form method="GET" action="{{ route('dashboard.comments') }}" class="mb-3 d-flex">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search by content">
        <button class="btn btn-primary">Search</button>
    </form> --}}
    
    <form method="GET" action="{{ route('dashboard.comments') }}" class="mb-3 row g-2">
        <div class="col-md-4">
            <input type="text" name="comment" value="{{ request('comment') }}" class="form-control" placeholder="Comment content">
        </div>
        <div class="col-md-4">
            <input type="text" name="game" value="{{ request('game') }}" class="form-control" placeholder="Game title">
        </div>
        <div class="col-md-4">
            <input type="text" name="user" value="{{ request('user') }}" class="form-control" placeholder="User name">
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    @if($comments->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Game</th>
                    <th>User</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                    <tr>
                        <td><a href="{{ route('game.view', $comment->game->game_id) }}">{{ $comment->game->title }}</a></td>
                        <td>{{ $comment->user->name }}</td>
                        <td>{{ Str::limit($comment->content, 80) }}</td>
                        <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $comments->withQueryString()->links('pagination::simple-bootstrap-5') }}
    @else
        <p>No comments found.</p>
    @endif
</div>
@endsection