@extends('layouts.app')

@section('title', 'Initialize Project')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('projects') }}" class="btn btn-sm btn-link text-decoration-none text-muted p-0 mb-3">
                <i class="bi bi-arrow-left"></i> Back to Blueprints
            </a>

            <div class="card border shadow-sm rounded-lg">
                <div class="card-header bg-white py-3"><h5 class="fw-bold text-gray-900 mb-0">Launch New Project Blueprint</h5></div>
                <div class="card-body p-4 bg-white">
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700">Project Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700">Operational Content Briefing</label>
                            <textarea name="content" rows="6" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_public" id="isPublicSwitch" checked>
                            <label class="form-check-label fw-semibold text-gray-700" for="isPublicSwitch">Make Project Space Public</label>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-info"><i class="bi bi-pencil-square"></i> Assign Editors</label>
                                <select name="editors[]" class="form-select" multiple style="height: 120px;">
                                    @foreach($users as $user)
                                        @if($user->id !== Auth::id())
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary"><i class="bi bi-person"></i> Assign Team Participants</label>
                                <select name="participants[]" class="form-select" multiple style="height: 120px;">
                                    @foreach($users as $user)
                                        @if($user->id !== Auth::id())
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('projects') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success px-4 shadow-sm">Initialize Workspace</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
