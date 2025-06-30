@extends('layouts.app')

@section('title', 'Edit Game')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Edit Game</h5>
                {{-- <form action="{{ route('games.update', ['id' => $game->game_id]) }}" method="POST"> --}}
                <form action="{{ route('games.update', ['id' => $game->game_id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Game Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $game->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" required>{{ $game->description }}</textarea>
                    </div>

                    {{-- <div class="mb-3">
                        <label for="img_src" class="form-label">Image URL</label>
                        <input type="url" name="img_src" class="form-control" value="{{ $game->img_src }}" required>
                    </div> --}}
                    <div class="mb-3">
                        <label class="form-label">Current Image:</label><br>
                        <img src="{{ asset($game->img_src) }}" alt="Current cover" style="max-width: 200px;">
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Game Cover Image</label>
                        <input type="file" name="image" class="form-control" accept=".png,.jpg,.jpeg,.webp" required>
                    </div>
                    
                    <input type="hidden" name="back_url" value="{{ $backUrl }}">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ $backUrl }}" class="btn btn-secondary me-2">Cancel</a>
                    {{-- <a href="{{ route('game.view', ['id' => $game->game_id]) }}" class="btn btn-secondary">Cancel</a> --}}
                </form>
            </div>
        </div>
    </div>
</div>
@endsection