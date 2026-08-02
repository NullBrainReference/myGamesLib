@extends('layouts.app')

@section('title', 'My Library')

@section('content')
    <main class="container my-4" x-data="{ viewMode: localStorage.getItem('library_view') || 'grid' }">
        
        {{-- Переключатель вида (Grid / List) --}}
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h2 class="h4 fw-bold text-gray-900 mb-0">My Library</h2>
            
            <div class="btn-group bg-light p-1 rounded-lg border border-gray-200" role="group">
                {{-- Кнопка Сетка --}}
                <button type="button" 
                        @click="viewMode = 'grid'; localStorage.setItem('library_view', 'grid')"
                        :class="viewMode === 'grid' ? 'bg-white shadow-sm text-dark' : 'text-gray-500'"
                        class="btn btn-sm border-0 rounded px-3 transition-all d-flex align-items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Grid</span>
                </button>

                {{-- Кнопка Список --}}
                <button type="button" 
                        @click="viewMode = 'list'; localStorage.setItem('library_view', 'list')"
                        :class="viewMode === 'list' ? 'bg-white shadow-sm text-dark' : 'text-gray-500'"
                        class="btn btn-sm border-0 rounded px-3 transition-all d-flex align-items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>List</span>
                </button>
            </div>
        </div>

        @forelse($games as $game)
            @if($loop->first)
                {{-- Обертка Сетки (Grid View) --}}
                <div x-show="viewMode === 'grid'" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @endif

            {{-- Карточка сетки --}}
            <template x-if="viewMode === 'grid'">
                <div class="col">
                    <x-library-game-card :game="$game" :can-delete="$canDelete ?? false" />
                </div>
            </template>

            @if($loop->last)
                </div>
            @endif
        @empty
            <p class="text-center text-muted mt-5 py-5">You don't have any games in your library yet.</p>
        @endforelse

        {{-- Обертка Списка (List View) --}}
        @if(count($games) > 0)
            <div x-show="viewMode === 'list'" class="d-flex flex-column gap-1 max-w-4xl mx-auto">
                {{-- Шапка списка (опционально) --}}
                <div class="d-flex justify-content-between text-xs text-uppercase fw-bold text-gray-400 px-3 mb-1">
                    <div>Title</div>
                    <div class="d-flex gap-4 me-10">
                        <div class="w-20 text-center">Type</div>
                        <div class="w-24 text-center">Rating</div>
                        <div class="w-20 text-end">Played</div>
                    </div>
                </div>

                @foreach($games as $game)
                    <x-library-game-list-item :game="$game" :can-delete="$canDelete ?? false" />
                @endforeach
            </div>
        @endif

    </main>
@endsection