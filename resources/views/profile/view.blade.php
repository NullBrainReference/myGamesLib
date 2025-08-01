@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card p-4">
                @if(auth()->check() && auth()->user()->isAdmin() && !$isSelf)
                    @if($user->isBanned())
                        <form action="{{ route('profile.unban', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Unban</button>
                        </form>
                    @else
                        <form action="{{ route('profile.ban', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger btn-sm">Ban</button>
                        </form>
                    @endif
                @endif

                @if($user->isBanned())
                    <div class="alert alert-danger mt-3">
                        This user is banned.
                    </div>
                @endif
                <div class="d-flex align-items-start">
                    

                    <div class="me-4 text-center">
                        @if($profile && $profile->avatar)
                            <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        
                        <div class="mt-2">
                            <a href="{{ route('library.user', $user->id) }}" class="btn btn-outline-primary btn-sm">
                                {{ $isSelf ? 'To your library' : "To $user->name's library" }}
                            </a>
                            {{-- <a href="{{ route('library.user', $user->id) }}" class="btn btn-outline-primary btn-sm">To {{ $user->name }}'s Library</a> --}}
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-1">{{ $user->name }}</h3>
                        <div class="mb-2">
                            <strong>Gender:</strong>
                            {{ $profile && $profile->gender ? __($profile->gender) : 'Was not set.' }}
                        </div>
                        <div class="mb-2">
                            <strong>Age:</strong>
                            @if($profile && $profile->birth_date)
                                {{ \Carbon\Carbon::parse($profile->birth_date)->age }} 
                            @else
                                Was not set.
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>Location:</strong>
                            {{ $profile && $profile->location ? $profile->location : 'Was not set.' }}
                        </div>

                    </div>
                </div>

                <div class="mt-4">
                    <h5>About me</h5>
                    <div class="border rounded p-3 bg-light" style="min-height: 60px;">

                        {{ $profile && $profile->about_me 
                            ? $profile->about_me 
                            : ($isSelf ? 'You wrote nothing about yourself.' : 'User wrote nothing about self.') 
                        }}

                    </div>
                </div>

                @if($isSelf)
                    <div class="mt-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">
                            Edit profile
                        </a>
                    </div>
                @endif

                @if($user->comments()->count())
                    <div class="mt-3">
                        <a href="{{ route('profile.comments', $user->id) }}" class="btn btn-outline-info btn-sm">
                            View comments ({{ $user->comments()->count() }})
                        </a>
                    </div>
                @endif

                <a href="{{ route('blog.author', $user->id) }}" class="btn btn-outline-primary btn-sm mt-3">
                    {{ $isSelf ? 'Your blog posts' : "$user->name's blog posts" }}
                </a>
            </div>

        </div>
    </div>
</div>
@endsection