<div class="position-relative library-card-container">

    <a href="{{ route('game.view', ['id' => $game->game_id]) }}" class="text-decoration-none text-reset card-link-wrapper">
        <div class="card h-100 border border-gray-200 shadow-sm transition-all duration-300 hover-lift bg-white rounded-lg overflow-hidden">

            <div class="ratio ratio-16x9 bg-light overflow-hidden">
                <img src="{{ asset($game->img_src) }}"
                     class="card-img-top object-fit-cover transition-transform duration-500 hover-scale"
                     alt="{{ $game->title }}"
                     loading="lazy">
            </div>

            <div class="card-body d-flex flex-column p-3">
                <div class="flex-grow-1">
                    <h5 class="card-title fw-bold text-gray-900 mb-1 text-truncate" title="{{ $game->title }}">
                        {{ $game->title }}
                    </h5>
                    <p class="card-text text-gray-500 small line-clamp-2 mb-0">
                        {{ $game->description }}
                    </p>
                </div>
            </div>
        </div>
    </a>

    @if($canDelete ?? false)
        <div class="position-absolute top-0 end-0 m-2.5" style="z-index: 20;">
            <form action="#" method="POST" onsubmit="return confirm('Remove {{ addslashes($game->title) }} from your library?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center p-2 border border-gray-200 shadow-sm hover-text-red transition" title="Remove from Library">
                    <svg class="h-4 w-4 text-gray-500 hover:text-red-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    @endif
</div>
