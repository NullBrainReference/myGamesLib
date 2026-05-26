@extends('layouts.app')

@section('title', 'Admin Dashboard - Games')

@section('content')
<div class="container py-4">
    <x-callback-message />

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Manage game catalog listings, update metadata information, and expand index entries.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-primary active px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
        </div>
    </div>

    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-12 col-md-5 col-lg-4">
                <form method="GET" action="{{ route('dashboard.games') }}">
                    <div class="input-group input-group-sm">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search games by title...">
                        <button class="btn btn-primary px-3" type="submit">Search</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-auto d-flex align-items-center gap-3 justify-content-md-end">
                @if(request('search'))
                    <a href="{{ route('dashboard.games') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none">Clear Filters</a>
                @endif
                <a href="{{ route('games.create') }}" class="btn btn-sm btn-success font-medium shadow-sm px-3 d-inline-flex align-items-center">
                    <svg class="h-4 w-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Game
                </a>
            </div>
        </div>
    </div>

    @if($games->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold" style="width: 80px;">Image</th>
                            <th class="py-3 font-semibold">Title</th>
                            <th class="py-3 font-semibold" style="width: 45%;">Description</th>
                            <th class="pe-4 py-3 text-end font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($games as $game)
                            <tr>
                                <td class="ps-4 py-2.5">
                                    <div class="ratio ratio-16x9 border border-gray-200 rounded overflow-hidden bg-light shadow-sm" style="width: 70px;">
                                        <img src="{{ asset($game->img_src) }}"
                                             class="object-fit-cover"
                                             alt="{{ $game->title }} cover">
                                    </div>
                                </td>

                                <td class="py-2.5 fw-semibold text-gray-900">
                                    {{ $game->title }}
                                </td>

                                <td class="py-2.5 text-gray-500 small">
                                    {{ Str::limit($game->description, 85) }}
                                </td>

                                <td class="pe-4 py-2.5 text-end">
                                    <div class="d-inline-flex gap-1.5 justify-content-end align-items-center">
                                        <a href="{{ route('game.view', $game->game_id) }}" class="btn btn-xs btn-outline-primary font-medium px-2.5 py-1 shadow-sm rounded text-decoration-none text-xs">
                                            View
                                        </a>
                                        <a href="{{ route('games.edit', $game->game_id) }}" class="btn btn-xs btn-outline-secondary font-medium px-2.5 py-1 shadow-sm rounded text-decoration-none text-xs">
                                            Edit
                                        </a>

                                        <form method="GET" action="{{ route('games.remove', $game->game_id) }}" class="m-0 p-0">
                                            <input type="hidden" name="back_url" value="{{ request()->fullUrl() }}">
                                            <button class="btn btn-xs btn-outline-danger font-medium px-2.5 py-1 shadow-sm rounded text-xs"
                                                    type="submit"
                                                    onclick="return confirm('Proceed to deletion prompt for {{ addslashes($game->title) }}?');">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 px-1">
            {{ $games->withQueryString()->links('pagination::simple-bootstrap-5') }}
        </div>
    @else
        <div class="card border border-gray-200 rounded-lg p-5 text-center bg-white shadow-sm">
            <div class="py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 001 1s1.5-.5 1.5 1.5-1.5 1.5-1.5 1.5a1 1 0 00-1 1v3a1 1 0 01-1 1h-3a1 1 0 00-1 1v1a2 2 0 11-4 0v-1a1 1 0 00-1-1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1s-1.5.5-1.5-1.5 1.5-1.5 1.5-1.5a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No matching games found</h5>
                <p class="text-gray-500 small mb-3">No platform game catalog listings match your current dashboard search constraints.</p>
                <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-secondary shadow-sm">Reset Catalog View</a>
            </div>
        </div>
    @endif
</div>

@endsection
