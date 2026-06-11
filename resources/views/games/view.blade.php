@extends('layouts.app')

@section('title', $game->title)

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm text-center mb-4 font-medium small rounded-lg">
            <i class="bi bi-check-circle-fill me-1.5"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm text-center mb-4 font-medium small rounded-lg">
            <i class="bi bi-exclamation-triangle-fill me-1.5"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">

        <div class="col-12 col-md-4 col-lg-3">
            <div class="card border border-gray-200 shadow-sm bg-white rounded-lg overflow-hidden sticky-md-top" style="top: 24px; z-index: 10;">
                <div class="ratio ratio-4x3 bg-light border-bottom border-gray-100">
                    <img src="{{ asset($game->img_src) }}" class="object-fit-cover w-100 h-100" alt="{{ $game->title }} catalog preview artwork">
                </div>

                <div class="card-body p-3">
                    @auth
                        @if(Auth::user()->games->contains($game->game_id))
                            <div class="d-flex flex-column gap-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle py-2.5 px-3 rounded text-xs font-semibold d-flex align-items-center justify-content-center gap-1.5 shadow-none">
                                    <i class="bi bi-check-circle-fill"></i> In Your Library
                                </span>
                                <form action="{{ route('library.remove', $game->game_id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 font-medium py-1.5 rounded shadow-sm text-xs">
                                        Remove from Library
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('library.add', ['id' => $game->game_id]) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary w-100 py-2.5 font-semibold text-xs rounded shadow-sm d-flex align-items-center justify-content-center gap-1.5">
                                    <i class="bi bi-plus-lg text-sm"></i> Add to Library
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="text-center py-1">
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary w-100 font-medium py-2 rounded text-xs">
                                Log in to Access Library
                            </a>
                        </div>
                    @endauth
                </div>

                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="card-footer bg-gray-50 border-top border-gray-200 py-2.5 px-3 d-flex justify-content-between align-items-center">
                            <small class="text-uppercase font-bold text-gray-500 tracking-wider" style="font-size: 0.65rem;">Admin Tools</small>
                            <div class="btn-group shadow-sm bg-white border border-gray-200 rounded">
                                <button type="button" class="btn btn-xs btn-outline-secondary font-medium px-2 py-1 text-xs d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#manageTagsModal">
                                    <i class="bi bi-tags"></i> Tags
                                </button>
                                <a href="{{ route('games.edit', ['id' => $game->game_id]) }}" class="btn btn-xs btn-outline-warning font-medium px-2 py-1 text-xs text-decoration-none">
                                    Edit
                                </a>
                                <form action="{{ route('games.remove', ['id' => $game->game_id]) }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger font-medium px-2 py-1 text-xs" onclick="return confirm('Completely purge this item record and metadata array from index catalogs?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-9">

            <div class="card border border-gray-200 shadow-sm bg-white rounded-lg p-4 p-md-5 mb-4">
                <h1 class="h2 fw-bold text-gray-900 tracking-tight mb-2">{{ $game->title }}</h1>

                @if($game->tags->isNotEmpty())
                    <div class="mb-4 d-flex flex-wrap gap-1.5">
                        @foreach($game->tags as $tag)
                            <span class="badge {{ $tag->is_r18 ? 'bg-danger text-white' : 'bg-gray-100 text-gray-700 border border-gray-200' }} px-2.5 py-1.5 font-medium rounded text-xs d-inline-flex align-items-center gap-1"
                                  static-bs-toggle="tooltip"
                                  title="{{ $tag->description }}">
                                @if($tag->is_r18)
                                    <i class="bi bi-exclamation-triangle-fill"></i> 18+
                                @endif
                                {{ $tag->title }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <hr class="my-4 opacity-10">

                <h3 class="h6 text-uppercase tracking-wider fw-bold text-gray-500 mb-2.5">Catalog Entry Overview Description</h3>
                <p class="text-gray-700 leading-relaxed text-sm mb-0 style-textarea-rendered" style="white-space: pre-line;">{{ $game->description }}</p>
            </div>

            <div class="card border border-gray-200 shadow-sm bg-white rounded-lg p-4 mb-4 text-center">
                <h6 class="text-gray-500 mb-2 text-uppercase tracking-wider fw-bold small">Global Community Metrics</h6>
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <div class="star-rating fs-4">
                        <div class="back-stars">★★★★★★★★★★</div>
                        <div class="front-stars" style="width: {{ ($game->average_rating / 10) * 100 }}%">★★★★★★★★★★</div>
                    </div>
                    <span class="fw-bold h4 text-gray-900 mb-0">{{ number_format($game->average_rating, 1) }}</span>
                    <span class="text-gray-400 small">({{ $game->ratings->count() }} evaluations indexed)</span>
                </div>

                @auth
                    <hr class="my-3 opacity-10">
                    @php $userRating = $game->ratings->where('user_id', Auth::id())->first(); @endphp

                    <p class="text-gray-500 mb-1.5 text-xs text-uppercase tracking-wider font-semibold">
                        {{ $userRating ? 'Your Established Input Score' : 'Log Rating Score Evaluation Parameters' }}
                    </p>

                    <form action="{{ route('rating.store', $game->game_id) }}" method="POST" id="rating-form" class="m-0">
                        @csrf
                        <div class="rating-wrapper mb-2">
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
                             <div class="small text-gray-500 font-medium">You configured this record valuation index target parameters to <strong class="text-amber-500">{{ $userRating->rating }}</strong>/10</div>
                        @endif
                    </form>
                @else
                    <div class="pt-2">
                        <a href="{{ route('login') }}" class="btn btn-xs btn-outline-primary px-3 rounded-pill font-medium text-xs">
                            Log in to write an evaluation score parameters card
                        </a>
                    </div>
                @endauth
            </div>

            <div class="card border border-gray-200 shadow-sm bg-white rounded-lg overflow-hidden mb-4">
                <div class="card-header bg-gray-50 border-bottom border-gray-200 py-3 px-4">
                    <h3 class="h5 fw-bold text-gray-900 mb-0">Editorial Content Breakdowns & Reviews</h3>
                </div>

                <div class="card-body p-4">
                    @auth
                        @if(!$userReview)
                            <div class="card border border-gray-200 rounded-lg p-3 bg-gray-50 mb-4">
                                <h6 class="font-semibold text-gray-800 text-sm mb-2 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-image text-gray-500"></i> Stage Review Embed Markdown Asset File Upload
                                </h6>
                                <form action="{{ route('image.upload-temp') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end m-0">
                                    @csrf
                                    <input type="hidden" name="title" value="{{ old('title') }}">
                                    <input type="hidden" name="content" value="{{ old('content') }}">
                                    <div class="col-12 col-sm-9">
                                        <input type="file" class="form-control form-control-sm bg-white" id="review_image" name="image" required>
                                    </div>
                                    <div class="col-12 col-sm-3 d-grid">
                                        <button type="submit" class="btn btn-sm btn-secondary font-medium">Upload File</button>
                                    </div>
                                </form>

                                @if(session('pending_images'))
                                    <div class="mt-4 border-top border-gray-200 pt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="font-semibold text-gray-800 text-xs text-uppercase tracking-wider mb-0">Staged Review Assets</h6>
                                            <form action="{{ route('image.clear-temp') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-danger font-medium py-1">Clear Assets Batch</button>
                                            </form>
                                        </div>
                                        <div class="row g-2">
                                            @foreach(session('pending_images') as $index => $imgPath)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <div class="card border border-gray-200 rounded overflow-hidden bg-white p-1 shadow-sm">
                                                        <div class="ratio ratio-4x3 overflow-hidden rounded border border-gray-100">
                                                            <img src="{{ asset($imgPath) }}" class="w-100 h-100 object-fit-cover" alt="Temporary upload asset tracking node thumbnail">
                                                        </div>
                                                        <div class="p-1.5 d-flex flex-column gap-1">
                                                            <button type="button" class="btn btn-xs btn-light border border-gray-200 text-gray-700 font-medium w-100 py-1 text-xs" onclick="copyToClipboard('markdown-{{ $index }}')">
                                                                Copy Link Syntax
                                                            </button>
                                                            <form action="{{ route('image.delete-temp', $index) }}" method="POST" class="m-0">
                                                                @csrf
                                                                <button type="submit" class="btn btn-xs btn-outline-danger w-100 py-1 text-xs">Wipe</button>
                                                            </form>
                                                        </div>
                                                        <pre style="display:none"><code id="markdown-{{ $index }}">![Embedded View Graphics Asset]({{ asset($imgPath) }})</code></pre>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <form action="{{ route('review.store', $game->game_id) }}" method="POST" class="mb-5 pb-4 border-bottom border-gray-100">
                                @csrf
                                <div class="mb-3">
                                    <label for="review_title" class="form-label fw-semibold text-gray-700 small">Review Headline</label>
                                    <input type="text" name="title" id="review_title" class="form-control" placeholder="Summarize core execution takeaways, balance properties, or performance..." value="{{ old('title') }}" required maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="review_content" class="form-label fw-semibold text-gray-700 small">Review Body Narrative Text <span class="text-muted font-normal">(Markdown typography rules syntax supported)</span></label>
                                    <textarea name="content" id="review_content" class="form-control" rows="7" placeholder="Provide clear granular breakdowns of gameplay patterns, balance logic systems, design concepts, or art presentation workflows..." required>{{ old('content') }}</textarea>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary font-medium px-4 py-2 shadow-sm">Publish Entry</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-medium px-3 py-2" onclick="clearReviewForm()">Reset Form</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info border-0 shadow-sm rounded-lg d-flex align-items-center justify-content-between p-3 mb-4 text-sm">
                                <span class="text-gray-800 font-medium">
                                    <i class="bi bi-info-circle-fill text-blue-600 me-1.5"></i> You have logged an explicit review entry for this catalog title index node.
                                </span>
                                <a href="#user-review" class="btn btn-xs btn-primary font-medium shadow-sm px-3 py-1.5 text-decoration-none">Focus Entry View</a>
                            </div>
                        @endif
                    @else
                        <div class="p-4 rounded-lg border border-dashed border-gray-200 text-center bg-gray-50 mb-4">
                            <p class="text-gray-500 text-sm mb-2.5">Want to construct an analytical breakdown write-up or map graphics capture embeds?</p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary font-medium px-4 py-2 shadow-sm text-decoration-none text-xs">Log in to write a review</a>
                        </div>
                    @endauth

                    @if($reviews->count() > 0)
                        <div class="d-flex flex-column gap-3.5">
                            @foreach($reviews as $review)
                                <div class="card border border-gray-200 rounded-lg bg-white p-4 shadow-sm" @if(Auth::check() && Auth::id() === $review->user_id) id="user-review" @endif>
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2.5">
                                        <div>
                                            <h5 class="h6 fw-bold text-gray-900 mb-0.5">{{ $review->title }}</h5>
                                            <div class="text-xs text-gray-400 font-medium">
                                                By <span class="text-gray-600 font-semibold">{{ $review->user->name }}</span> — Logged on {{ $review->created_at->format('M d, Y') }}
                                            </div>
                                        </div>

                                        @if(Auth::check() && (Auth::id() === $review->user_id || Auth::user()->isAdmin()))
                                            <div class="btn-group shadow-sm bg-white border border-gray-200 rounded">
                                                <button class="btn btn-xs btn-outline-secondary font-medium px-2.5 py-1 text-xs" onclick="editReview({{ $review->id }}, '{{ addslashes($review->title) }}', '{{ addslashes($review->content) }}')">Modify</button>
                                                <form action="{{ route('review.delete', $review->id) }}" method="POST" class="d-inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger font-medium px-2.5 py-1 text-xs" onclick="return confirm('Permanently wipe this specific documentation text data node?')">Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-gray-700 text-sm leading-relaxed review-rendered-markdown-output pt-1">
                                        {!! Parsedown::instance()->text($review->content) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 px-1">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-5 text-gray-400 text-sm border border-dashed border-gray-100 rounded-lg">
                            <i class="bi bi-chat-left-quote d-block fs-2 mb-2 text-gray-300"></i>
                            No comprehensive reviews have been documented yet. Be the first to catalog your tracking feedback!
                        </div>
                    @endif
                </div>
            </div>

            <x-game-mechanics :game="$game" />

            <div class="card border border-gray-200 shadow-sm bg-white rounded-lg p-3 p-md-4">
                <x-comment-section :object="$game" type="game" :comments="$comments" />
            </div>

        </div>
    </div>
</div>

@auth
    @if(Auth::user()->isAdmin())
        <div class="modal fade" id="manageTagsModal" tabindex="-1" aria-labelledby="manageTagsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg bg-white rounded-lg">
                    <div class="modal-header border-bottom border-gray-100 py-3 px-4">
                        <h5 class="modal-title fw-bold text-gray-900 h6" id="manageTagsModalLabel">Configure Classification Links</h5>
                        <button type="button" class="btn-close shadow-none text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="input-group mb-3 shadow-sm rounded border border-gray-300 overflow-hidden bg-white">
                            <span class="input-group-text bg-white border-0 text-gray-400 pe-1">
                                <i class="bi bi-search text-xs"></i>
                            </span>
                            <input type="text" id="tagSearchInput"
                                class="form-control border-0 ps-1 text-sm bg-white form-control-sm"
                                placeholder="Type to filter global catalog items...">
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-gray-100 pb-2">
                            <span class="text-xs font-bold text-gray-400 text-uppercase tracking-wider">Indexed Identifiers</span>
                            <button type="button" class="btn btn-xs btn-primary font-medium shadow-sm px-2.5 py-1 text-xs d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createTagModal">
                                <i class="bi bi-plus-lg"></i> Create Global Tag
                            </button>
                        </div>

                        <div class="list-group list-group-flush border rounded border-gray-200 overflow-hidden max-h-60" id="tagListGroup" style="max-height: 320px;">
                            @if($allTags)
                                @foreach($allTags as $tag)
                                    @php $hasTag = $game->tags->contains('tag_id', $tag->tag_id); @endphp
                                    <div class="list-group-item d-flex justify-content-between align-items-center tag-item-row bg-white p-2.5 border-bottom border-gray-100 text-sm" data-title="{{ strtolower($tag->title) }}">
                                        <div class="pe-2 text-truncate" style="max-width: 280px;">
                                            <span class="fw-bold text-gray-900 d-inline-flex align-items-center gap-1">
                                                {{ $tag->title }}
                                                @if($tag->is_r18)
                                                    <span class="badge bg-danger rounded-sm py-0.5 px-1 font-semibold" style="font-size:0.6rem">18+</span>
                                                @endif
                                            </span>
                                            <small class="text-gray-400 text-truncate d-block text-xs font-normal" title="{{ $tag->description }}">{{ $tag->description }}</small>
                                        </div>

                                        <div>
                                            @if($hasTag)
                                                <form action="{{ route('games.tags.detach', ['game_id' => $game->game_id, 'tag_id' => $tag->tag_id]) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger px-2.5 py-1 rounded text-xs font-medium shadow-sm">Remove</button>
                                                </form>
                                            @else
                                                <form action="{{ route('games.tags.attach', $game->game_id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="tag_id" value="{{ $tag->tag_id }}">
                                                    <button type="submit" class="btn btn-xs btn-outline-success px-3 py-1 rounded text-xs font-medium shadow-sm">Add</button>
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

        <div class="modal fade" id="createTagModal" tabindex="-1" aria-labelledby="createTagModalLabel" aria-hidden="true" style="background: rgba(17, 24, 39, 0.45); backdrop-filter: blur(2px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border border-gray-200 shadow-xl bg-white rounded-lg overflow-hidden">
                    <div class="modal-header bg-gray-900 border-0 py-3 px-4 text-white">
                        <h5 class="modal-title fw-bold h6 d-flex align-items-center gap-1.5 text-white" id="createTagModalLabel">
                            <i class="bi bi-tag-fill text-blue-400"></i> Append Global Platform Classification Tag
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('tags.store') }}" method="POST" class="m-0">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="new_tag_title" class="form-label fw-semibold text-gray-700 small">Tag Label title</label>
                                <input type="text" name="title" id="new_tag_title" class="form-control" placeholder="e.g., Tactical RPG, Roguelite, Open World" required maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label for="new_tag_description" class="form-label fw-semibold text-gray-700 small">System Metric Context Definition Description</label>
                                <textarea name="description" id="new_tag_description" class="form-control" rows="3" placeholder="Provide context explanations regarding bounds of content matching this classification tag..." maxlength="1000"></textarea>
                            </div>

                            <div class="form-check form-switch p-2.5 ps-5 border border-gray-200 rounded bg-gray-50 mt-4 d-flex align-items-center justify-content-between">
                                <label class="form-check-label fw-semibold text-danger small cursor-pointer" for="new_tag_r18">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Restrict Index Scope as Mature Audiences 18+ (R-18)
                                </label>
                                <input class="form-check-input ms-0 cursor-pointer shadow-none relative" style="right: 12px;" type="checkbox" role="switch" id="new_tag_r18" name="is_r18" value="1">
                            </div>
                        </div>

                        <div class="modal-footer bg-gray-50 border-top border-gray-100 py-2.5 px-4 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-xs btn-outline-secondary font-medium px-3 py-1.5 rounded" data-bs-toggle="modal" data-bs-target="#manageTagsModal">Return to Grid</button>
                            <button type="submit" class="btn btn-xs btn-primary font-semibold px-4 py-1.5 shadow-sm rounded">Save Tag Configuration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mechanics dialog --}}
        <div id="mechanicModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" onclick="closeMechanicModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">

                    <div class="bg-gray-900 px-4 py-3 sm:px-6 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white tracking-wide uppercase flex items-center gap-2" id="mechanicModalTitle">
                            Setup Mechanic Parameters
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-white text-lg focus:outline-none" onclick="closeMechanicModal()">
                            &times;
                        </button>
                    </div>

                    <form id="mechanicForm" method="POST" class="m-0">
                        @csrf
                        <div id="mechanicFormMethod"></div>

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="mechanic_title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Mechanic Title Label</label>
                                    <input type="text" name="title" id="mechanic_title"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2.5 text-sm"
                                           placeholder="e.g., Worker Placement, Deck Building" required maxlength="255">
                                </div>

                                <div>
                                    <label for="mechanic_content" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">System Rule Logic Description</label>
                                    <textarea name="content" id="mechanic_content" rows="4"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2.5 text-sm"
                                              placeholder="Define exactly how this dynamic architectural mechanic constraints execution behaviors..." required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-center border-t border-gray-100">
                            <button type="button"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none"
                                    onclick="closeMechanicModal()">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded text-xs font-semibold shadow-md hover:bg-blue-700 focus:outline-none">
                                Save Structural Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endauth

<script>
// Link Copier Utility Execution
function copyToClipboard(elementId) {
    const codeElement = document.getElementById(elementId);
    const text = codeElement.textContent || codeElement.innerText;
    navigator.clipboard.writeText(text).catch(err => {
        console.error('Failed to copy asset tracking reference block line: ', err);
    });
}

// Inline Form Local Data Persistence Management Layer Loop
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

// Real-time Text Filter Execution Node
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tagSearchInput');
    const tagRows = document.querySelectorAll('.tag-item-row');

    function filterTags(query) {
        tagRows.forEach(row => {
            const tagTitle = row.getAttribute('data-title');
            if (!query || tagTitle.includes(query)) {
                row.style.setProperty('display', '', 'important');
                row.classList.add('d-flex');
            } else {
                row.style.setProperty('display', 'none', 'important');
                row.classList.remove('d-flex');
            }
        });
    }

    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        filterTags(query);
    });

    const manageTagsModal = document.getElementById('manageTagsModal');
    if (manageTagsModal) {
        manageTagsModal.addEventListener('show.bs.modal', function() {
            if (searchInput) searchInput.value = '';
            filterTags('');
        });
    }
});
</script>
@endsection
