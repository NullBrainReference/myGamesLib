@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('profile.view', $user->id) }}" class="btn btn-outline-secondary btn-sm me-3">
                        &larr; Back to Profile
                    </a>
                    <h4 class="mb-0">{{ $user->name }}'s Friends ({{ $friends->count() }})</h4>
                </div>

                @forelse($friends as $friend)
                    <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <img src="{{ $friend->profile && $friend->profile->avatar ? asset('storage/' . $friend->profile->avatar) : asset('images/default-avatar.png') }}"
                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <a href="{{ route('profile.view', $friend->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $friend->name }}
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">This user has no friends yet.</p>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
