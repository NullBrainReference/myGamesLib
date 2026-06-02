@extends('layouts.app')

@section('title', 'Forum Threads')

@section('content')
    <x-callback-message />

    {{-- Header Options Context Row --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3 mb-4 mx-3">
        {{-- Search Input Form --}}
        <form method="GET" action="{{ route('forum') }}" class="d-flex flex-grow-1 max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search threads by title...">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </form>

        {{-- Authentication Check for Creating Threads --}}
        @auth
            <a href="{{ route('forum.threads.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm text-nowrap">
                <i class="bi bi-chat-left-text"></i> Start new thread
            </a>
        @endauth
    </div>

    {{-- Main Threads Display Area --}}
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                @forelse($threads as $thread)
                    {{-- Clean loop injection of our custom row --}}
                    <x-forum-thread-row :thread="$thread" />
                @empty
                    <div class="text-center py-5 border rounded-lg bg-light shadow-sm">
                        <i class="bi bi-chat-square-text text-muted h1 d-block mb-3"></i>
                        <h5 class="text-gray-700 fw-bold">No threads discovered</h5>
                        <p class="text-muted small mb-0">Be the first to share an observation or question!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pagination Controls --}}
    <div class="d-flex flex-column align-items-center mb-5 gap-2">
        {{ $threads->links('pagination::simple-bootstrap-5') }}
    </div>
@endsection
