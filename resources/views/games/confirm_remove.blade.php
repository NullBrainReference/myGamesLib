@extends('layouts.app')

@section('title', 'Confirm Deletion')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">Delete {{ $game->title }}?</h5>
                <p class="card-text">Are you sure you want to remove this game from the database?</p>

                <form action="{{ route('games.delete', ['id' => $game->game_id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    <a href="{{ route('game.view', ['id' => $game->game_id]) }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection