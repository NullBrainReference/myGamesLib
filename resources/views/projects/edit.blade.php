@extends('layouts.app')

@section('title', 'Modify Workspace')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('projects.view', $project->id) }}" class="btn btn-sm btn-link text-decoration-none text-muted p-0 mb-3">
                <i class="bi bi-arrow-left"></i> Cancel and Return
            </a>

            <div class="card border shadow-sm rounded-lg">
                <div class="card-header bg-white py-3"><h5 class="fw-bold text-gray-900 mb-0">Modify Architecture: {{ $project->title }}</h5></div>
                <div class="card-body p-4 bg-white">
                    <form method="POST" action="{{ route('projects.update', $project->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700">Project Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $project->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Icons Upload Fields & Preview --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-gray-700">Main Icon (Big)</label>
                                @if($project->icon_big)
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $project->icon_big) }}" alt="Big Icon" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="text-xs text-muted">Current Big Icon</span>
                                    </div>
                                @endif
                                <input type="file" name="icon_big" class="form-control @error('icon_big') is-invalid @enderror" accept="image/*">
                                @error('icon_big') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-gray-700">Secondary Icon (Small)</label>
                                @if($project->icon_small)
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $project->icon_small) }}" alt="Small Icon" class="rounded border" style="width: 30px; height: 30px; object-fit: cover;">
                                        <span class="text-xs text-muted">Current Small Icon</span>
                                    </div>
                                @endif
                                <input type="file" name="icon_small" class="form-control @error('icon_small') is-invalid @enderror" accept="image/*">
                                @error('icon_small') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700">Operational Content Briefing (Markdown Supported)</label>
                            <textarea name="content" rows="6" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $project->content) }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_public" id="isPublicSwitch" {{ $project->is_public ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-gray-700" for="isPublicSwitch">Make Project Space Public</label>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-info"><i class="bi bi-pencil-square"></i> Update Editors</label>
                                <select name="editors[]" class="form-select" multiple style="height: 120px;">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $project->editors->contains($user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary"><i class="bi bi-people"></i> Update Participants</label>
                                <select name="participants[]" class="form-select" multiple style="height: 120px;">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $project->participants->contains($user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('projects.view', $project->id) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection