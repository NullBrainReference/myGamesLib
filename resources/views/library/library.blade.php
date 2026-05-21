@extends('layouts.app')

@section('title', 'My Library')

@section('content')
    <main class="mt-3">
        @forelse($games as $game)
            <div class="d-flex justify-content-center flex-wrap mb-5">
                <div class="w-25">
                    <x-library-game-card :game="$game" :can-delete="$canDelete ?? false" />
                </div>
            </div>
        @empty
            <p class="text-center text-muted mt-5">You don't have any games in your library yet.</p>
        @endforelse
    </main>
@endsection
