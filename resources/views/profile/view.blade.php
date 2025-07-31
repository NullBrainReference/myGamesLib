@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card p-4">
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
            </div>

        </div>
    </div>
</div>
@endsection