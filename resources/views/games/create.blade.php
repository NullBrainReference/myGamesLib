@extends('layouts.app')

@section('title', 'Add New Game')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('dashboard.games') }}" class="text-decoration-none text-gray-600 hover:text-gray-900 small d-inline-flex align-items-center gap-1">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Games Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card border border-gray-200 shadow-sm bg-white rounded-lg overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    <div class="mb-4">
                        <h2 class="h4 fw-bold text-gray-900 mb-1">Create New Game</h2>
                        <p class="text-gray-500 small mb-0">Index a new entry into the global platform catalog. Ensure all required assets fulfill configuration requirements.</p>
                    </div>

                    <form action="{{ route('games.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold text-gray-700 small">Game Title</label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title') }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="e.g., The Witcher 3: Wild Hunt"
                                   required>
                            @error('title')
                                <div class="invalid-feedback font-medium small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-gray-700 small">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="5"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Provide an engaging overview of the title's storyline, mechanics, and core features..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback font-medium small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold text-gray-700 small">Game Cover Image</label>
                            <input type="file"
                                   name="image"
                                   id="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept=".png,.jpg,.jpeg,.webp"
                                   required>
                            <div class="form-text text-gray-400 text-xs mt-1.5">
                                Accepts PNG, JPG, JPEG, or WEBP formats. Recommended aspect ratio layout: 16:9. Max size limit: 2MB.
                            </div>
                            @error('image')
                                <div class="invalid-feedback font-medium small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 mt-5 pt-3 border-top border-gray-100">
                            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary font-medium px-3.5 py-2 rounded text-decoration-none shadow-sm">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary font-medium px-4 py-2 rounded shadow-sm">
                                Create Game
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
