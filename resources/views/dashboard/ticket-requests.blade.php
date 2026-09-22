@extends('layouts.app')

@section('title', 'Admin Dashboard - Position Ticket Requests')

@section('content')
<style>
    /* Strip default browser number input spinners for clean alignment */
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
            <p class="text-gray-500 small mb-0">Review project slot expansion requests and adjust ticket allocations for open positions.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
            <a href="{{ route('dashboard.ticket-requests') }}" class="btn btn-sm btn-primary active px-3">Ticket Requests</a>
        </div>
    </div>

    {{-- Search Filter Card --}}
    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-12 col-md-5 col-lg-4">
                <form method="GET" action="{{ route('dashboard.ticket-requests') }}">
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
                    <a href="{{ route('dashboard.ticket-requests') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none">Clear Filters</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Ticket Requests Table --}}
    @if($requests->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold">Project & Position</th>
                            <th class="py-3 font-semibold text-center" style="width: 110px;">Requested</th>
                            <th class="py-3 font-semibold" style="width: 35%;">Reason</th>
                            <th class="py-3 font-semibold text-center" style="width: 120px;">Status</th>
                            <th class="pe-4 py-3 text-end font-semibold" style="width: 240px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $ticketReq)
                            <tr>
                                {{-- Project & Position Info --}}
                                <td class="ps-4 py-2.5">
                                    <div class="fw-bold text-gray-900">
                                        {{ $ticketReq->hirePosition->title ?? 'N/A' }}
                                    </div>
                                    <div class="small text-gray-500">
                                        Project:
                                        @if(optional($ticketReq->hirePosition)->project)
                                            <a href="{{ route('projects.view', $ticketReq->hirePosition->project->id) }}" class="text-decoration-none text-indigo-600 font-medium">
                                                {{ $ticketReq->hirePosition->project->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Unlinked</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Requested Quantity --}}
                                <td class="py-2.5 text-center fw-bold text-indigo-600">
                                    +{{ $ticketReq->quantity }}
                                </td>

                                {{-- Reason --}}
                                <td class="py-2.5 text-gray-600 small">
                                    {{ Str::limit($ticketReq->reason, 110) }}
                                </td>

                                {{-- Status --}}
                                <td class="py-2.5 text-center">
                                    @if($ticketReq->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">
                                            Approved (+{{ $ticketReq->granted_quantity }})
                                        </span>
                                    @elseif($ticketReq->status === 'declined')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded">
                                            Declined
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                {{-- Action Form Buttons --}}
                                <td class="pe-4 py-2.5 text-end">
                                    @if($ticketReq->status === 'pending')
                                        <div class="d-inline-flex gap-1.5 align-items-center justify-content-end">
                                            {{-- Approve Form with Compact Input --}}
                                            <form method="POST" action="{{ route('dashboard.ticket-requests.approve', $ticketReq->id) }}" class="d-inline-flex align-items-center gap-1 m-0">
                                                @csrf
                                                <input type="number"
                                                       name="granted_quantity"
                                                       value="{{ old('granted_quantity', $ticketReq->quantity) }}"
                                                       min="1"
                                                       required
                                                       title="Adjust granted ticket amount"
                                                       class="form-control text-center fw-bold border-gray-300 rounded shadow-none"
                                                       style="width: 46px; height: 28px; padding: 0 4px; font-size: 0.75rem;">

                                                <button type="submit"
                                                        class="btn btn-success fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;">
                                                    Approve
                                                </button>
                                            </form>

                                            {{-- Decline Form --}}
                                            <form method="POST" action="{{ route('dashboard.ticket-requests.decline', $ticketReq->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-outline-danger fw-semibold px-2.5 shadow-sm rounded d-inline-flex align-items-center justify-content-center"
                                                        style="height: 28px; font-size: 0.75rem;"
                                                        onclick="return confirm('Are you sure you want to decline this request?');">
                                                    Decline
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Resolved {{ $ticketReq->updated_at->diffForHumans() }}</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No ticket requests found</h5>
                <p class="text-gray-500 small mb-3">No project position slot requests match your current filters.</p>
                <a href="{{ route('dashboard.ticket-requests') }}" class="btn btn-sm btn-secondary shadow-sm">Reset View</a>
            </div>
        </div>
    @endif
</div>
@endsection
