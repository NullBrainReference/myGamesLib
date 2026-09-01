@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-tabs card-header-tabs" id="friendTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends" type="button">
                                Friends <span class="badge bg-primary ms-1">{{ $friends->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="incoming-tab" data-bs-toggle="tab" data-bs-target="#incoming" type="button">
                                Requests <span class="badge bg-danger ms-1">{{ $incomingRequests->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button">
                                Sent Requests <span class="badge bg-secondary ms-1">{{ $sentRequests->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="friendTabsContent">

                        {{-- Friends Tab --}}
                        <div class="tab-pane fade show active" id="friends" role="tabpanel">
                            @forelse($friends as $friend)
                                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $friend->profile && $friend->profile->avatar ? asset('storage/' . $friend->profile->avatar) : asset('images/default-avatar.png') }}"
                                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('profile.view', $friend->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $friend->name }}
                                            </a>
                                        </div>
                                    </div>
                                    <form action="{{ route('friends.remove', $friend->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">Unfriend</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">You don't have any friends added yet.</p>
                            @endforelse
                        </div>

                        {{-- Incoming Requests Tab --}}
                        <div class="tab-pane fade" id="incoming" role="tabpanel">
                            @forelse($incomingRequests as $requestUser)
                                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $requestUser->profile && $requestUser->profile->avatar ? asset('storage/' . $requestUser->profile->avatar) : asset('images/default-avatar.png') }}"
                                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <a href="{{ route('profile.view', $requestUser->id) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $requestUser->name }}
                                        </a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('friends.accept', $requestUser->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-success btn-sm">Accept</button>
                                        </form>
                                        <form action="{{ route('friends.remove', $requestUser->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-secondary btn-sm">Decline</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">No pending friend requests.</p>
                            @endforelse
                        </div>

                        {{-- Sent Requests Tab --}}
                        <div class="tab-pane fade" id="sent" role="tabpanel">
                            @forelse($sentRequests as $sentUser)
                                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $sentUser->profile && $sentUser->profile->avatar ? asset('storage/' . $sentUser->profile->avatar) : asset('images/default-avatar.png') }}"
                                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <a href="{{ route('profile.view', $sentUser->id) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $sentUser->name }}
                                        </a>
                                    </div>
                                    <form action="{{ route('friends.remove', $sentUser->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary btn-sm">Cancel Request</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0">No outgoing friend requests.</p>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
