@extends('layouts.app')

@section('title', 'Admin Dashboard - Project Approval Requests')

@section('content')
<div class="container py-4">
    <x-callback-message />

    {{-- Top Header & Navigation Tabs --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Review and resolve community-voted project creation & approval requests.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
            <a href="{{ route('dashboard.ticket-requests') }}" class="btn btn-sm btn-outline-secondary px-3">Ticket Extensions</a>
            <a href="{{ route('dashboard.position-requests') }}" class="btn btn-sm btn-outline-secondary px-3">Position Requests</a>
            <a href="{{ route('dashboard.project-requests') }}" class="btn btn-sm btn-primary active px-3">Project Approvals</a>
        </div>
    </div>

    {{-- Search Filter Card --}}
    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-12 col-md-5 col-lg-4">
                <form method="GET" action="{{ route('dashboard.project-requests') }}">
                    <div class="input-group input-group-sm">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search by project or requester name...">
                        <button class="btn btn-primary px-3" type="submit">Search</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-auto d-flex align-items-center gap-3 justify-content-md-end">
                @if(request('search'))
                    <a href="{{ route('dashboard.project-requests') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none">Clear Filters</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Project Requests Table --}}
    @if($requests->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold">Project & Requester</th>
                            <th class="py-3 font-semibold">Voting Comment Thread</th>
                            <th class="py-3 font-semibold text-center" style="width: 140px;">Requested Date</th>
                            <th class="py-3 font-semibold text-center" style="width: 120px;">Status</th>
                            <th class="pe-4 py-3 text-end font-semibold" style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $req)
                            <tr>
                                {{-- Project & Requester Info --}}
                                <td class="ps-4 py-2.5">
                                    <div class="fw-bold text-gray-900">
                                        @if($req->project)
                                            <a href="{{ route('projects.view', $req->project->id) }}" class="text-decoration-none text-indigo-600 hover:text-indigo-800 font-bold">
                                                {{ $req->project->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Unlinked Project</span>
                                        @endif
                                    </div>
                                    <div class="small text-gray-500">
                                        Requested by: 
                                        @if($req->user)
                                            <span class="font-medium text-gray-700">{{ $req->user->name }}</span>
                                        @else
                                            <span class="text-muted">Unknown User</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Linked Comment Thread --}}
                                <td class="py-2.5 text-gray-600 small">
                                    @if($req->comment)
                                        <div class="text-truncate" style="max-width: 320px;">
                                            <span class="badge bg-light text-dark border me-1">Comment #{{ $req->comment->id }}</span>
                                            {{ Str::limit($req->comment->content, 90) }}
                                        </div>
                                    @else
                                        <span class="text-muted italic">No linked comment</span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="py-2.5 text-center text-gray-500 small">
                                    {{ $req->created_at->format('M d, Y') }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-2.5 text-center">
                                    @if($req->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">
                                            Approved
                                        </span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                {{-- Action Form Buttons --}}
                                <td class="pe-4 py-2.5 text-end">
                                    @if($req->status === 'pending')
                                        <div class="d-inline-flex gap-1.5 align-items-center justify-content-end">
                                            {{-- Approve Button --}}
                                            <form method="POST" action="{{ route('dashboard.project-requests.approve', $req->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-success fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;"
                                                        onclick="return confirm('Approve this project and sync participants/editors?');">
                                                    Approve
                                                </button>
                                            </form>

                                            {{-- Reject Button --}}
                                            <form method="POST" action="{{ route('dashboard.project-requests.reject', $req->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-outline-danger fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;"
                                                        onclick="return confirm('Reject this project approval request?');">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Resolved {{ $req->updated_at->diffForHumans() }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 px-1">
            {{ $requests->links('pagination::simple-bootstrap-5') }}
        </div>
    @else
        <div class="card border border-gray-200 rounded-lg p-5 text-center bg-white shadow-sm">
            <div class="py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No project approval requests found</h5>
                <p class="text-gray-500 small mb-3">There are no pending or matching project approval requests at this time.</p>
                <a href="{{ route('dashboard.project-requests') }}" class="btn btn-sm btn-secondary shadow-sm">Reset View</a>
            </div>
        </div>
    @endif
</div>
@endsection