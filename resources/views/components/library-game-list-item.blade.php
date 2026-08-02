@props(['game', 'canDelete' => false])

<div x-data="{ showPreview: false }" 
     :class="{ 'z-50': showPreview, 'z-0': !showPreview }"
     class="d-flex align-items-center justify-content-between bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:shadow transition-all mb-2 position-relative">
    
    {{-- 1. Название с Alpine.js Hover Preview --}}
    <div class="flex-grow-1 min-w-0 pe-3 position-relative">
        <a href="{{ route('game.view', ['id' => $game->game_id]) }}" 
           @mouseenter="showPreview = true" 
           @mouseleave="showPreview = false"
           class="fw-bold text-gray-900 text-decoration-none hover:text-blue-600 text-truncate d-inline-block max-w-full align-middle py-1">
            {{ $game->title }}
        </a>

        {{-- Floating Preview Card --}}
        <div x-show="showPreview"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 transform translate-y-1"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-1"
             style="display: none; position: absolute; bottom: 100%; left: 0; z-index: 9999;"
             class="mb-2 w-64 p-2 bg-white rounded-lg shadow-xl border border-gray-200 pointer-events-none">
            
            <div class="ratio ratio-16x9 rounded overflow-hidden mb-2 bg-light">
                <img src="{{ asset($game->img_src) }}" alt="{{ $game->title }}" class="w-100 h-100 object-fit-cover">
            </div>
            <div class="fw-bold text-gray-900 text-sm mb-1 text-truncate">{{ $game->title }}</div>
            <p class="text-gray-500 text-xs mb-0 line-clamp-2">{{ $game->description }}</p>
        </div>
    </div>

    {{-- 2. Мета-данные игры (Тип, Оценка, Часы) --}}
    <div class="d-flex align-items-center gap-4 text-sm text-gray-600 flex-shrink-0 me-3">
        
        {{-- Тип (Заглушка) --}}
        <div class="w-20 text-center">
            <span class="badge bg-light text-dark border border-gray-200 fw-normal px-2 py-1">
                Game
            </span>
        </div>

        {{-- Оценка пользователя --}}
        <div class="w-24 d-flex align-items-center justify-content-center gap-1">
            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="fw-semibold text-gray-800">
                {{ $game->average_rating ? number_format($game->average_rating, 1) : '-' }}
            </span>
        </div>

        {{-- Игровое время (Заглушка) --}}
        <div class="w-20 text-end text-gray-500">
            0 hrs
        </div>
    </div>

    {{-- 3. Кнопка удаления --}}
    @if($canDelete ?? false)
        <div class="flex-shrink-0 ms-2">
            <form action="#" method="POST" onsubmit="return confirm('Delete {{ addslashes($game->title) }} from library?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-sm btn-light rounded-circle p-1 border-0 hover-text-red transition" 
                        title="Delete from library">
                    <svg class="h-4 w-4 text-gray-400 hover:text-red-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    @endif
</div>