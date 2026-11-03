@extends('layouts.app')

@section('title', 'Admin Dashboard - Mechanic Proposals')

@section('content')
<div class="container py-4">
    <x-callback-message />

    {{-- Top Header & Navigation Tabs --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Review and resolve community-voted mechanic proposals & variants.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
            <a href="{{ route('dashboard.ticket-requests') }}" class="btn btn-sm btn-outline-secondary px-3">Ticket Extensions</a>
            <a href="{{ route('dashboard.position-requests') }}" class="btn btn-sm btn-outline-secondary px-3">Position Requests</a>
            <a href="{{ route('dashboard.project-requests') }}" class="btn btn-sm btn-outline-secondary px-3">Project Approvals</a>
            <a href="{{ route('dashboard.mechanics') }}" class="btn btn-sm btn-primary active px-3">Mechanics</a>
        </div>
    </div>

    {{-- Search Filter & Action Card --}}
    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-12 col-md-5 col-lg-4">
                <form method="GET" action="{{ route('dashboard.mechanics') }}">
                    <div class="input-group input-group-sm">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search by title, author, game or project...">
                        <button class="btn btn-primary px-3" type="submit">Search</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-md-end">
                @if(request('search'))
                    <a href="{{ route('dashboard.mechanics') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none me-2">Clear Filters</a>
                @endif

                {{-- Batch Processing Trigger Button --}}
                <form method="POST" action="{{ route('dashboard.mechanics.process-batch') }}" class="m-0">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm text-white bg-indigo-600 hover:bg-indigo-700 font-bold px-3 py-1.5 rounded shadow-sm flex items-center gap-1"
                            onclick="return confirm('Evaluate all pending mechanics against community voting rules (>=3 votes, >=75% upvote rate)?');">
                        <span>⚙️</span> Run Batch Approvals
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Mechanics Table --}}
    @if($mechanics->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold">Mechanic & Author</th>
                            <th class="py-3 font-semibold">Context / Target</th>
                            <th class="py-3 font-semibold">Voting & Comment Thread</th>
                            <th class="py-3 font-semibold text-center" style="width: 130px;">Submitted</th>
                            <th class="py-3 font-semibold text-center" style="width: 110px;">Status</th>
                            <th class="pe-4 py-3 text-end font-semibold" style="width: 190px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($mechanics as $mech)
                            <tr>
                                {{-- Title & Author --}}
                                <td class="ps-4 py-2.5">
                                    <div class="fw-bold text-gray-900">
                                        {{ $mech->title }}
                                        @if($mech->isVariant())
                                            <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.65rem;">Variant</span>
                                        @endif
                                    </div>
                                    <div class="small text-gray-500">
                                        Proposed by:
                                        @if($mech->user)
                                            <span class="font-medium text-gray-700">{{ $mech->user->name }}</span>
                                        @else
                                            <span class="text-muted">System / Admin</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Context (Game / Project / Parent) --}}
                                <td class="py-2.5 text-gray-600 small">
                                    @if($mech->project)
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle me-1">Project</span>
                                        <a href="{{ route('projects.view', $mech->project->id) }}" class="text-decoration-none font-medium text-indigo-600">
                                            {{ $mech->project->title }}
                                        </a>
                                    @elseif($mech->game)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Game</span>
                                        <span class="font-medium text-gray-800">{{ $mech->game->title }}</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Generic / Global</span>
                                    @endif

                                    @if($mech->parent)
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            Parent: <span class="italic">{{ $mech->parent->title }}</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Voting Thread & Stats --}}
                                <td class="py-2.5 text-gray-600 small">
                                    @if($mech->comment)
                                        @php
                                            $totalVotes = $mech->comment->votes->count();
                                            $upvotes = $mech->comment->votes->where('vote', '>', 0)->count();
                                            $upvoteRate = $totalVotes > 0 ? round(($upvotes / $totalVotes) * 100) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark border">Comment #{{ $mech->comment->id }}</span>
                                            <span class="fw-bold {{ $upvoteRate >= 75 && $totalVotes >= 3 ? 'text-success' : 'text-gray-500' }}">
                                                👍 {{ $upvotes }}/{{ $totalVotes }} ({{ $upvoteRate }}%)
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted italic">Direct submission</span>
                                    @endif
                                </td>

                                {{-- Submitted Date --}}
                                <td class="py-2.5 text-center text-gray-500 small">
                                    {{ $mech->created_at?->format('M d, Y') ?? 'N/A' }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-2.5 text-center">
                                    @if($mech->approved)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="pe-4 py-2.5 text-end">
                                    @if(!$mech->approved)
                                        <div class="d-inline-flex gap-1.5 align-items-center justify-content-end">
                                            {{-- Approve --}}
                                            <form method="POST" action="{{ route('dashboard.mechanics.approve', $mech->mechanic_id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-success fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;"
                                                        onclick="return confirm('Approve and publish this mechanic proposal?');">
                                                    Approve
                                                </button>
                                            </form>

                                            {{-- Reject --}}
                                            <form method="POST" action="{{ route('dashboard.mechanics.reject', $mech->mechanic_id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-outline-danger fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;"
                                                        onclick="return confirm('Reject and delete this mechanic proposal?');">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Published</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 px-1">
            {{ $mechanics->links('pagination::simple-bootstrap-5') }}
        </div>
    @else
        <div class="card border border-gray-200 rounded-lg p-5 text-center bg-white shadow-sm">
            <div class="py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No mechanic proposals found</h5>
                <p class="text-gray-500 small mb-3">There are no pending or matching mechanic proposals at this time.</p>
                <a href="{{ route('dashboard.mechanics') }}" class="btn btn-sm btn-secondary shadow-sm">Reset View</a>
            </div>
        </div>
    @endif
</div>
@endsection
