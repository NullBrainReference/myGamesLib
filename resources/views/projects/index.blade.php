@extends('layouts.app')

@section('title', 'Projects')

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
        {{-- @auth
            <a href="{{ route('forum.threads.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm text-nowrap">
                <i class="bi bi-chat-left-text"></i> Start new project
            </a>
        @endauth --}}
    </div>

    {{-- Projects list Display Area --}}
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">

            </div>
        </div>
    </div>

    {{-- Pagination Controls --}}
    <div class="d-flex flex-column align-items-center mb-5 gap-2">
        {{ $projects->links('pagination::simple-bootstrap-5') }}
    </div>
@endsection
