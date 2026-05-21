@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <x-callback-message />

    <main class="mt-3">
        @if($latestGame)
            <div class="d-flex justify-content-center flex-wrap mb-5">
                <div class="w-25">
                    <x-game-card :game="$latestGame" />
                </div>
            </div>
        @else
            <p class="text-center text-muted">No games available.</p>
        @endif
    </main>
@endsection
