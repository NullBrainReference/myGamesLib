@props(['thread'])

<a href="{{ route('forum.thread', ['id' => $thread->id]) }}" class="text-decoration-none text-reset d-block mb-3 thread-link-wrapper">
    <div class="card border border-gray-200 shadow-sm transition-all duration-300 hover-lift bg-white rounded-lg p-3">
        <div class="row align-items-center">

            {{-- Thread Main Information --}}
            <div class="col-md-8 col-sm-12">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    {{-- Pin/Lock status if applicable --}}
                    @if($thread->is_pinned)
                        <span class="badge bg-warning text-dark small"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
                    @endif
                    @if($thread->is_locked)
                        <span class="badge bg-secondary small"><i class="bi bi-lock-fill"></i> Locked</span>
                    @endif

                    <h5 class="card-title fw-bold text-gray-900 mb-0 text-truncate" title="{{ $thread->title }}">
                        {{ $thread->title }}
                    </h5>
                </div>

                {{-- Metadata Row --}}
                <div class="text-muted small d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold text-gray-700">
                        <i class="bi bi-person-circle"></i> {{ $thread->user->name }}
                    </span>
                    <span class="text-gray-400">•</span>
                    <span>Posted {{ $thread->created_at->diffForHumans() }}</span>

                    {{-- Related Game Tag Identifier (Optional highlight) --}}
                    {{-- @if($thread->game)
                        <span class="text-gray-400">•</span>
                        <span class="badge bg-blue-50 text-blue-600 border border-blue-200 px-2 py-0.5" style="font-size: 0.75rem;">
                            {{ $thread->game->title }}
                        </span>
                    @endif --}}
                </div>
            </div>

            {{-- Thread Engagement Metrics (Replies / Activity) --}}
            <div class="col-md-4 col-sm-12 mt-3 mt-md-0 d-flex justify-content-md-end align-items-center gap-4 text-center">
                {{-- Comments/Replies Count Column --}}
                <div class="px-2">
                    <span class="d-block fw-bold h5 text-gray-800 mb-0">{{ $thread->comments_count ?? $thread->comments->count() }}</span>
                    <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Replies</small>
                </div>

                {{-- Clean arrow prompt like your game card --}}
                <div class="ps-2 text-blue-600 group">
                    <svg class="h-5 w-5 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

        </div>
    </div>
</a>
