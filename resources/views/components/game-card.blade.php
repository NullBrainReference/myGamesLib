@props(['game'])

<a href="{{ route('game.view', ['id' => $game->game_id]) }}" 
   class="block w-full h-full group no-underline text-inherit">
    <div class="flex flex-col h-full border border-gray-200 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md bg-white rounded-lg overflow-hidden">
        
        {{-- Aspect Ratio 16:9 Image Container --}}
        <div class="aspect-video w-full bg-gray-100 overflow-hidden shrink-0">
            <img src="{{ asset($game->img_src) }}"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                 alt="{{ $game->title }}"
                 loading="lazy">
        </div>

        {{-- Card Content --}}
        <div class="flex flex-col flex-1 p-4">
            <div class="flex-1">
                <h5 class="font-bold text-gray-900 text-sm mb-2 truncate" title="{{ $game->title }}">
                    {{ $game->title }}
                </h5>

                <p class="text-gray-600 text-xs line-clamp-3 leading-relaxed mb-0">
                    {{ $game->description }}
                </p>
            </div>

            {{-- Footer Link --}}
            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100 shrink-0">
                <span class="text-xs font-semibold text-blue-600">View Game</span>
                <svg class="h-4 w-4 text-blue-600 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>
</a>