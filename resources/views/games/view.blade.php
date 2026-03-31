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
    <div class="col-md-8 mt-3 w-25">
        <div class="card border rounded p-3">
            <img src="{{ $game->img_src }}" class="card-img-top" alt="{{ $game->title }}">
            <div class="card-body">
                <h5 class="card-title">{{ $game->title }}</h5>
                <p class="card-text">{{ $game->description }}</p>

                @auth
                @if(Auth::user()->games->contains($game->game_id))
                    <span class="badge bg-success">In Your Library</span>
                    <form action="{{ route('library.remove', $game->game_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить из библиотеки">
                            Delete from my Library
                        </button>
                    </form>
                @else
                    <form action="{{ route('library.add', ['id' => $game->game_id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Add to Library</button>
                    </form>
                @endif
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('games.edit', ['id' => $game->game_id]) }}"
                        class="btn btn-warning mt-1">
                        Edit Game
                    </a>

                    <form action="{{ route('games.remove', ['id' => $game->game_id]) }}" method="GET" class="mt-1">
                            <input type="hidden" name="back_url" value="{{ $backUrl }}">
                            <button type="submit" class="btn btn-danger">Delete Game</button>
                        </form>
                @endif
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- Ratings Section --}}
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="text-muted">Average Rating:
                    <div class="star-rating d-inline-block">
                        <div class="back-stars">★★★★★★★★★★</div>
                        <div class="front-stars" style="width: {{ ($game->average_rating / 10) * 100 }}%">★★★★★★★★★★</div>
                    </div>
                    ({{ $game->average_rating }}/10, {{ $game->ratings->count() }} ratings)
                </h5>

                @if(Auth::check())
                    @php $userRating = $game->ratings->where('user_id', Auth::id())->first(); @endphp
                    <hr class="my-3">
                    <form action="{{ route('rating.store', $game->game_id) }}" method="POST" class="d-inline">
                        @csrf
                        <label for="rating" class="me-2 text-muted">Your Rating (1-10):</label>
                        <select name="rating" id="rating" class="form-select form-select-sm d-inline w-auto me-2" required>
                            <option value="">Select</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ ($userRating->rating ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Rate</button>
                    </form>
                @else
                    <p class="text-muted mt-2"><a href="{{ route('login') }}" class="text-decoration-none">Log in to rate</a></p>
                @endif
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
</script>

@endsection

@section('comments')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <x-comment-section :object="$game" type="game" :comments="$comments" />
    </div>
</div>
@endsection
