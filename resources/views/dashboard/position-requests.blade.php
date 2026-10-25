@extends('layouts.app')

@section('title', 'Admin Dashboard - Position Slot Requests')

@section('content')
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="container py-4">
    <x-callback-message />

    {{-- Top Header & Navigation Tabs --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Approve or reject requests for entirely new project vacancy roles.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
            <a href="{{ route('dashboard.ticket-requests') }}" class="btn btn-sm btn-outline-secondary px-3">Ticket Extensions</a>
            <a href="{{ route('dashboard.position-requests') }}" class="btn btn-sm btn-primary active px-3">Position Requests</a>
        </div>
    </div>

    {{-- Search Filter Card --}}
    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-12 col-md-5 col-lg-4">
                <form method="GET" action="{{ route('dashboard.position-requests') }}">
                    <div class="input-group input-group-sm">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search by project or position title...">
                        <button class="btn btn-primary px-3" type="submit">Search</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-auto d-flex align-items-center gap-3 justify-content-md-end">
                @if(request('search'))
                    <a href="{{ route('dashboard.position-requests') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none">Clear Filters</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Position Requests Table --}}
    @if($requests->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold">Project & Requested Role</th>
                            <th class="py-3 font-semibold text-center" style="width: 110px;">Slots</th>
                            <th class="py-3 font-semibold" style="width: 35%;">Reason / Scope</th>
                            <th class="py-3 font-semibold text-center" style="width: 120px;">Status</th>
                            <th class="pe-4 py-3 text-end font-semibold" style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $posReq)
                            <tr>
                                {{-- Project & Position Info --}}
                                <td class="ps-4 py-2.5">
                                    <div class="fw-bold text-gray-900">
                                        {{ $posReq->title }}
                                    </div>
                                    <div class="small text-gray-500">
                                        Project:
                                        @if($posReq->project)
                                            <a href="{{ route('projects.view', $posReq->project->id) }}" class="text-decoration-none text-indigo-600 font-medium">
                                                {{ $posReq->project->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Unlinked</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Requested Quantity --}}
                                <td class="py-2.5 text-center fw-bold text-emerald-600">
                                    {{ $posReq->quantity }} Slot(s)
                                </td>

                                {{-- Reason --}}
                                <td class="py-2.5 text-gray-600 small">
                                    {{ Str::limit($posReq->reason, 110) }}
                                </td>

                                {{-- Status --}}
                                <td class="py-2.5 text-center">
                                    @if($posReq->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">
                                            Approved
                                        </span>
                                    @elseif($posReq->status === 'rejected')
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
                                    @if($posReq->status === 'pending')
                                        <div class="d-inline-flex gap-1.5 align-items-center justify-content-end">
                                            {{-- Approve Form --}}
                                            <form method="POST" action="{{ route('dashboard.position-requests.approve', $posReq->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-success fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;">
                                                    Approve
                                                </button>
                                            </form>

                                            {{-- Decline Form --}}
                                            <form method="POST" action="{{ route('dashboard.position-requests.decline', $posReq->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-outline-danger fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;"
                                                        onclick="return confirm('Decline this position slot request?');">
                                                    Decline
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Resolved {{ $posReq->updated_at->diffForHumans() }}</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No position slot requests found</h5>
                <p class="text-gray-500 small mb-3">No new vacancy position requests match your current filters.</p>
                <a href="{{ route('dashboard.position-requests') }}" class="btn btn-sm btn-secondary shadow-sm">Reset View</a>
            </div>
        </div>
    @endif
</div>
@endsection