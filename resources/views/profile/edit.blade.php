@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card p-4">
                <h3>Edit Profile</h3>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <input type="file" class="form-control" id="avatar" name="avatar">
                        @if($profile && $profile->avatar)
                            <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" class="rounded mt-2" style="width: 80px; height: 80px; object-fit: cover;">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Birth date</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $profile->birth_date ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Not set</option>
                            <option value="male" @selected(($profile->gender ?? '') === 'male')>Male</option>
                            <option value="female" @selected(($profile->gender ?? '') === 'female')>Female</option>
                            <option value="other" @selected(($profile->gender ?? '') === 'other')>Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $profile->location ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="about_me" class="form-label">About me</label>
                        <textarea class="form-control" id="about_me" name="about_me" rows="4">{{ old('about_me', $profile->about_me ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="{{ route('profile.view', $user->id) }}" class="btn btn-secondary ms-2">Cancel</a>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection