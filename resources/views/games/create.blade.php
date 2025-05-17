@extends('layouts.app')

@section('title', 'Add New Game')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Create New Game</h5>

                <form action="{{ route('games.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Game Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="img_src" class="form-label">Image URL</label>
                        <input type="url" name="img_src" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Game</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection