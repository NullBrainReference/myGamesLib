@extends('layouts.app')

@section('title', 'Projects Blueprint')

@section('content')
<div class="container mb-5">

    {{-- Search and Filter Toolbar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3 mb-4 mx-3">
        <form method="GET" action="{{ route('projects') }}" class="d-flex flex-grow-1 max-w-xl gap-2" id="projectFilterForm">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search projects by title or tech...">

            <select name="sort" class="form-select w-auto" onchange="document.getElementById('projectFilterForm').submit();">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                <option value="alpha" {{ request('sort') == 'alpha' ? 'selected' : '' }}>Alphabetical (A-Z)</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
            </select>
            @auth
                <a href="{{ route('projects.create') }}" class="btn btn-success text-nowrap shadow-sm">
                    <i class="bi bi-plus-lg"></i> Launch Project Node
                </a>
            @endauth

            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </form>
    </div>

    {{-- Project Grid Matrix --}}
    <div class="row row-cols-1 row-cols-md-2 g-4 mx-2">
        @forelse($projects as $project)
            <div class="col">
                <a href="{{ route('projects.view', $project->id) }}" class="text-decoration-none text-reset">
                    <div class="card h-100 border border-gray-200 shadow-sm transition-all duration-300 hover-lift bg-white rounded-lg overflow-hidden">
                        <div class="card-body d-flex p-4 gap-3">

                            {{-- Project Small Icon Slot --}}
                            <div class="flex-shrink-0">
                                @if($project->icon_small)
                                    <img src="{{ asset($project->icon_small) }}" alt="Icon" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded border d-flex align-items-center justify-content-center text-secondary font-bold" style="width: 48px; height: 48px;">
                                        <i class="bi bi-kanban"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Summary Content Details --}}
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h5 class="card-title fw-bold text-gray-900 mb-0 text-truncate">{{ $project->title }}</h5>
                                    @if($project->is_public)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Public</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Private</span>
                                    @endif
                                </div>

                                <p class="card-text text-gray-600 small line-clamp-2 mb-2">
                                    {{ $project->content }}
                                </p>

                                {{-- Mini team counter summary badges --}}
                                <div class="d-flex gap-2 text-muted text-xs align-items-center">
                                    <small><i class="bi bi-shield-lock"></i> Lead: <strong>{{ $project->owners->first()->name ?? 'System' }}</strong></small>
                                    <small class="text-gray-300">•</small>
                                    <small><i class="bi bi-people"></i> {{ $project->participants->count() }} members</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5 border rounded-lg bg-light">
                <i class="bi bi-folder-x text-muted h1 d-block mb-3"></i>
                <h5 class="text-gray-700 fw-bold">No projects discovered matching filters.</h5>
            </div>
        @endforelse
    </div>

    {{-- Simple Pagination Integration --}}
    <div class="d-flex flex-column align-items-center mt-5 gap-2">
        {{ $projects->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection
