@extends('layouts.app')

@section('title', 'Games')

@section('content')
    <x-callback-message />

    {{-- Toolbar: Search & Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3 my-4 px-4">
        <form method="GET" action="{{ route('shop') }}" class="flex items-center gap-2 w-full sm:w-auto flex-1 max-w-md">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" 
                   placeholder="Search games by title">
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-colors shrink-0">
                Search
            </button>
        </form>

        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('games.create') }}" 
                   class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm shrink-0">
                    + Add New Game
                </a>
            @endif
        @endauth
    </div>

    {{-- Compact Tailwind Grid --}}
    <div class="px-4 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($games as $game)
                <x-game-card :game="$game" />
            @endforeach
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex flex-col items-center gap-2 my-6">
        {{ $games->links() }}
    </div>
@endsection