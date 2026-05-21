<a href="{{ route('game.view', ['id' => $game->game_id]) }}" class="text-decoration-none text-reset card-link-wrapper">
    <div class="card h-100 border border-gray-200 shadow-sm transition-all duration-300 hover-lift bg-white rounded-lg overflow-hidden">

        <div class="ratio ratio-16x9 bg-light overflow-hidden">
            <img src="{{ asset($game->img_src) }}"
                 class="card-img-top object-fit-cover transition-transform duration-500 hover-scale"
                 alt="{{ $game->title }}"
                 loading="lazy">
        </div>

        <div class="card-body d-flex flex-column p-4">
            <div class="flex-grow-1">
                <h5 class="card-title fw-bold text-gray-900 mb-2 text-truncate" title="{{ $game->title }}">
                    {{ $game->title }}
                </h5>

                <p class="card-text text-gray-600 small line-clamp-3 mb-0">
                    {{ $game->description }}
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-t border-gray-100">
                <span class="text-sm font-semibold text-blue-600">View Game</span>
                <svg class="h-4 w-4 text-blue-600 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>
</a>
