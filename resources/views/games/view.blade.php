@extends('layouts.app')

@section('title', $game->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <img src="{{ $game->img_src }}" class="card-img-top" alt="{{ $game->title }}">
            <div class="card-body">
                <h5 class="card-title">{{ $game->title }}</h5>
                <p class="card-text">{{ $game->description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
