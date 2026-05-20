@extends('layouts.app')

@section('title', $game->title)

@section('content')

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger text-center">
        {{ session('error') }}
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4 mt-3"> <div class="card h-100 border-0 shadow-sm">
            <img src="{{ $game->img_src }}" class="card-img-top" alt="{{ $game->title }}">

            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold text-truncate">{{ $game->title }}</h5>

                {{-- Game Tags Section --}}
                @if($game->tags->isNotEmpty())
                    <div class="mb-3 d-flex flex-wrap gap-1">
                        @foreach($game->tags as $tag)
                            <span class="badge {{ $tag->is_r18 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary border border-secondary' }}"
                                static-bs-toggle="tooltip"
                                title="{{ $tag->description }}">
                                @if($tag->is_r18)
                                    <i class="bi bi-exclamation-triangle-fill small"></i> 18+
                                @endif
                                {{ $tag->title }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <p class="card-text text-muted small mb-4">{{ $game->description }}</p>

                <div class="mt-auto"> @auth
                        @if(Auth::user()->games->contains($game->game_id))
                            <div class="d-grid gap-2">
                                <span class="badge bg-success-subtle text-success border border-success mb-2 p-2">
                                    <i class="bi bi-check-circle"></i> In Your Library
                                </span>
                                <form action="{{ route('library.remove', $game->game_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        Remove from Library
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('library.add', ['id' => $game->game_id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="bi bi-plus-lg"></i> Add to Library
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            @auth
                @if(Auth::user()->isAdmin())
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Admin Tools</small>
                        <div class="btn-group border-0">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#manageTagsModal">
                                <i class="bi bi-tags"></i> Tags
                            </button>

                            <a href="{{ route('games.edit', ['id' => $game->game_id]) }}" class="btn btn-sm btn-outline-warning">Edit</a>

                            <form action="{{ route('games.remove', ['id' => $game->game_id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>

{{-- Ratings Section --}}
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2 text-uppercase fw-bold" style="letter-spacing: 1px;">Community Score</h6>
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="star-rating me-2">
                        <div class="back-stars">★★★★★★★★★★</div>
                        <div class="front-stars" style="width: {{ ($game->average_rating / 10) * 100 }}%">★★★★★★★★★★</div>
                    </div>
                    <span class="fw-bold h5 mb-0">{{ number_format($game->average_rating, 1) }}</span>
                    <span class="text-muted ms-1 small">({{ $game->ratings->count() }} reviews)</span>
                </div>

                @auth
                    <hr class="my-4 opacity-25">

                    @php $userRating = $game->ratings->where('user_id', Auth::id())->first(); @endphp

                    <p class="text-muted mb-2 small fw-bold text-uppercase">
                        {{ $userRating ? 'Your Rating' : 'Rate this game' }}
                    </p>

                    <form action="{{ route('rating.store', $game->game_id) }}" method="POST" id="rating-form">
                        @csrf
                        <div class="rating-wrapper mb-3">
                            @for($i = 10; $i >= 1; $i--)
                                <input type="radio"
                                       id="star{{ $i }}"
                                       name="rating"
                                       value="{{ $i }}"
                                       {{ ($userRating && $userRating->rating == $i) ? 'checked' : '' }}
                                       onchange="document.getElementById('rating-form').submit();">
                                <label for="star{{ $i }}" title="{{ $i }} stars">★</label>
                            @endfor
                        </div>

                        @if($userRating)
                             <div class="small text-muted">You rated this a <strong>{{ $userRating->rating }}</strong>/10</div>
                        @endif
                    </form>
                @else
                    <div class="py-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                            Log in to rate
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- Reviews Section --}}
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Reviews</h5>
            </div>
            <div class="card-body">

                @auth
                    @if(!$userReview)
                        {{-- image upload + keep review text --}}
                        <div class="card mb-4 p-3">
                            <h6>Upload Images for Review</h6>
                            <form action="{{ route('image.upload-temp') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="title" value="{{ old('title') }}">
                                <input type="hidden" name="content" value="{{ old('content') }}">
                                <div class="mb-3">
                                    <label for="review_image" class="form-label">Choose image</label>
                                    <input type="file" class="form-control" id="review_image" name="image" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>

                            @if(session('pending_images'))
                                <div class="mt-3">
                                    <h6>🖼️ Uploaded Images</h6>
                                    <form action="{{ route('image.clear-temp') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-2">Clear All Images</button>
                                    </form>
                                    <div class="row gx-2 gy-2">
                                        @foreach(session('pending_images') as $index => $imgPath)
                                            <div class="col-6 col-md-4">
                                                <div class="ratio ratio-4x3 overflow-hidden rounded border">
                                                    <img src="{{ asset($imgPath) }}" class="w-100 h-100 object-fit-cover" alt="Uploaded preview">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-block mt-2 w-100" onclick="copyToClipboard('markdown-{{ $index }}')">
                                                    Copy
                                                </button>
                                                <form action="{{ route('image.delete-temp', $index) }}" method="POST" class="d-inline mt-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                                </form>
                                                <pre style="display:none"><code id="markdown-{{ $index }}">![Alt text]({{ asset($imgPath) }})</code></pre>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- review form --}}
                        <form action="{{ route('review.store', $game->game_id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="review_title" class="form-label">Review Title</label>
                                <input type="text" name="title" id="review_title" class="form-control" value="{{ old('title') }}" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label for="review_content" class="form-label">Review Content (Markdown supported)</label>
                                <textarea name="content" id="review_content" class="form-control" rows="8" required>{{ old('content') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="clearReviewForm()">Clear</button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            You have already submitted a review for this game. <a href="#user-review" class="alert-link">Edit your review</a>.
                        </div>
                    @endif
                @else
                    <p class="text-muted"><a href="{{ route('login') }}">Log in to write a review</a></p>
                @endauth

                @if($reviews->count() > 0)
                    @foreach($reviews as $review)
                        <div class="review-item mb-3 p-3 border rounded">
                            <h6>{{ $review->title }}</h6>
                            {{-- Render content as Markdown --}}
                            <div>{!! Parsedown::instance()->text($review->content) !!}</div>
                            <small class="text-muted">By {{ $review->user->name }} on {{ $review->created_at->format('M d, Y') }}</small>
                            @if(Auth::check() && (Auth::id() === $review->user_id || Auth::user()->isAdmin()))
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editReview({{ $review->id }}, '{{ $review->title }}', '{{ addslashes($review->content) }}')">Edit</button>
                                    <form action="{{ route('review.delete', $review->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this review?')">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    {{ $reviews->links() }}
                @else
                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
function copyToClipboard(elementId) {
    const codeElement = document.getElementById(elementId);
    const text = codeElement.textContent || codeElement.innerText;
    navigator.clipboard.writeText(text).catch(err => {
        console.error('Failed to copy: ', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.querySelector('form[action*="image.upload-temp"]');
    const reviewTitle = document.getElementById('review_title');
    const reviewContent = document.getElementById('review_content');

    if (reviewTitle && localStorage.getItem('review_title')) {
        reviewTitle.value = localStorage.getItem('review_title');
    }
    if (reviewContent && localStorage.getItem('review_content')) {
        reviewContent.value = localStorage.getItem('review_content');
    }

    const syncHidden = () => {
        if (!uploadForm || !reviewTitle || !reviewContent) return;
        const hiddenTitle = uploadForm.querySelector('input[name="title"]');
        const hiddenContent = uploadForm.querySelector('input[name="content"]');
        if (hiddenTitle) hiddenTitle.value = reviewTitle.value;
        if (hiddenContent) hiddenContent.value = reviewContent.value;
    };

    reviewTitle?.addEventListener('input', () => {
        localStorage.setItem('review_title', reviewTitle.value);
    });
    reviewContent?.addEventListener('input', () => {
        localStorage.setItem('review_content', reviewContent.value);
    });

    uploadForm?.addEventListener('submit', function() {
        syncHidden();
        localStorage.setItem('review_title', reviewTitle.value);
        localStorage.setItem('review_content', reviewContent.value);
    });

    const reviewForm = document.querySelector('form[action*="review.store"]');
    reviewForm?.addEventListener('submit', function() {
        localStorage.removeItem('review_title');
        localStorage.removeItem('review_content');
    });
});

function clearReviewForm() {
    const reviewTitle = document.getElementById('review_title');
    const reviewContent = document.getElementById('review_content');
    if (reviewTitle) reviewTitle.value = '';
    if (reviewContent) reviewContent.value = '';
    localStorage.removeItem('review_title');
    localStorage.removeItem('review_content');
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tagSearchInput');
    const tagRows = document.querySelectorAll('.tag-item-row');

    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();

        tagRows.forEach(row => {
            const tagTitle = row.getAttribute('data-title');
            if (tagTitle.includes(query)) {
                row.classList.set('d-flex', true);
                row.style.display = ''; // Shows the element matches
            } else {
                row.classList.remove('d-flex');
                row.style.display = 'none'; // Hides mismatches
            }
        });
    });
});
</script>

<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <x-comment-section :object="$game" type="game" :comments="$comments" />
    </div>
</div>

{{-- Admin Tags Management Modal --}}
@auth
    @if(Auth::user()->isAdmin())
        <div class="modal fade" id="manageTagsModal" tabindex="-1" aria-labelledby="manageTagsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="manageTagsModalLabel">Manage Game Tags</h5>
                        <button type="button" class="btn-close" data-bs-shadow="none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Look Up Search Row --}}
                        <div class="input-group mb-3 shadow-sm rounded border-2 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="tagSearchInput"
                                class="form-control border-0 ps-0 rounded-end"
                                placeholder="Type to filter tags...">
                        </div>

                        <button type="button"
                            class="btn btn-primary text-nowrap shadow-sm
                                d-flex align-items-center gap-1
                                mb-1"
                            data-bs-toggle="modal"
                            data-bs-target="#createTagModal"
                            >

                            <i class="bi bi-plus-lg"></i> New Tag
                        </button>

                        {{-- Selection List View --}}
                        <div class="list-group" id="tagListGroup">
                            @if($allTags)
                            @foreach($allTags as $tag)
                                @php
                                    $hasTag = $game->tags->contains('tag_id', $tag->tag_id);
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center tag-item-row" data-title="{{ strtolower($tag->title) }}">
                                    <div>
                                        <span class="fw-bold d-block">
                                            {{ $tag->title }}
                                            @if($tag->is_r18)
                                                <span class="badge bg-danger ms-1" style="font-size:0.65rem">18+</span>
                                            @endif
                                        </span>
                                        <small class="text-muted text-wrap d-block" style="font-size: 0.8rem;">{{ $tag->description }}</small>
                                    </div>

                                    <div>
                                        @if($hasTag)
                                            {{-- Detach form --}}
                                            <form action="{{ route('games.tags.detach', ['game_id' => $game->game_id, 'tag_id' => $tag->tag_id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger px-3 rounded-pill">Remove</button>
                                            </form>
                                        @else
                                            {{-- Attach form --}}
                                            <form action="{{ route('games.tags.attach', $game->game_id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="tag_id" value="{{ $tag->tag_id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success px-3 rounded-pill">Add</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth

{{-- Admin Create New Tag Secondary Modal --}}
@auth
    @if(Auth::user()->isAdmin())
        <div class="modal fade" id="createTagModal" tabindex="-1" aria-labelledby="createTagModalLabel" aria-hidden="true" style="background: rgba(0,0,0,0.3);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="createTagModalLabel"><i class="bi bi-tag-fill"></i> Create Global Tag</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('tags.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new_tag_title" class="form-label fw-semibold">Tag Name</label>
                                <input type="text" name="title" id="new_tag_title" class="form-control" placeholder="e.g., RPG, Sandbox, Strategy" required maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label for="new_tag_description" class="form-label fw-semibold">Description</label>
                                <textarea name="description" id="new_tag_description" class="form-wrap form-control" rows="3" placeholder="Briefly explain what this tag means..." maxlength="1000"></textarea>
                            </div>

                            <div class="form-check form-switch p-2 ps-5 border rounded bg-light">
                                <input class="form-check-input ms-3 float-end" type="checkbox" role="switch" id="new_tag_r18" name="is_r18" value="1">
                                <label class="form-check-label fw-semibold text-danger" for="new_tag_r18">
                                    <i class="bi bi-exclamation-circle-fill"></i> Restrict Tag as 18+ (R-18)
                                </label>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#manageTagsModal">Back to List</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4">Save Tag</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

@endsection

