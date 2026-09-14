@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-callback-message />

    <!-- 1. Latest Games Grid (Adaptive 2 to 8 Items) -->
    <section class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900 mb-0">🎮 New & Updated Games</h3>
            <a href="{{ route('shop') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">Game Catalog →</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-3">
            @forelse($latestGames as $game)
                <a href="{{ route('game.view', $game->game_id) }}" 
                   class="group flex flex-col h-full bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition text-gray-900 no-underline">
                    
                    <img src="{{ $game->img_src ?? 'https://via.placeholder.com/200x120' }}" 
                         class="w-full h-28 object-cover" 
                         alt="{{ $game->title }}">

                    <div class="p-2.5 flex flex-col justify-between flex-grow">
                        <h6 class="text-xs font-semibold truncate text-gray-900 group-hover:text-indigo-600 transition mb-1" title="{{ $game->title }}">
                            {{ $game->title }}
                        </h6>
                        
                        <!-- Muted Grayscale Rating -->
                        <div class="flex items-center gap-1 text-xs text-gray-400 mt-1">
                            <span class="text-gray-400">★</span>
                            <span class="font-medium text-gray-500">{{ $game->average_rating }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full"><p class="text-gray-500 text-sm">No games available yet.</p></div>
            @endforelse
        </div>
    </section>

    <!-- 2. Main Grid: Discussions + User Hub -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Left Column (2/3): Threads and News -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Trending Threads -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center font-bold text-gray-900">
                    <span>🔥 Trending Discussions</span>
                    <a href="{{ route('forum') }}" class="text-xs text-indigo-600 hover:underline">All Threads</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($popularThreads as $thread)
                        <a href="{{ route('forum.thread', $thread->id) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition text-gray-900 no-underline">
                            <div>
                                <div class="font-semibold text-sm text-gray-800">{{ $thread->title }}</div>
                                <span class="text-xs text-gray-500">By: {{ $thread->user->name }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">💬 {{ $thread->comments_count }}</span>
                        </a>
                    @empty
                        <div class="p-4 text-sm text-gray-500">No discussions yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- News & Posts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 font-bold text-gray-900">📰 News & Updates</div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($latestBlogs as $blog)
                            <div class="border border-gray-200 rounded-lg p-3.5 flex flex-col justify-between h-full">
                                <div>
                                    <h6 class="font-bold text-sm text-gray-900 truncate mb-1">{{ $blog->title }}</h6>
                                    <p class="text-xs text-gray-500 line-clamp-3">{{ Str::limit(strip_tags($blog->content), 70) }}</p>
                                </div>
                                <span class="text-xs text-gray-400 mt-3 block">{{ $blog->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 col-span-full mb-0">No news available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (1/3): Personal Shortcuts -->
        <div class="lg:col-span-1 space-y-4">
            <!-- User / Featured Project -->
            <div class="bg-gray-50 rounded-lg p-5 shadow-sm border border-gray-200">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">
                    {{ Auth::check() ? 'My Active Project' : 'Featured Project' }}
                </span>
                @if($currentProject)
                    <h5 class="font-bold text-lg text-gray-900 mb-1">{{ $currentProject->title }}</h5>
                    <p class="text-xs text-gray-600 mb-4 line-clamp-3">{{ Str::limit($currentProject->content, 90) }}</p>
                    <a href="{{ route('projects.view', $currentProject->id) }}" class="inline-block w-full text-center px-3 py-2 border border-gray-800 text-xs font-semibold rounded-md text-gray-800 hover:bg-gray-800 hover:text-white transition">View Project</a>
                @else
                    <p class="text-xs text-gray-500 mb-0">No active projects found.</p>
                @endif
            </div>

            <!-- Recent Library Game -->
            @auth
                <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-3">Recently in Library</span>
                    @if($lastLibraryGame)
                        <div class="flex items-center gap-3">
                            <img src="{{ $lastLibraryGame->img_src ?? 'https://via.placeholder.com/50' }}" class="rounded-md w-12 h-12 object-cover">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-sm text-gray-900 truncate">{{ $lastLibraryGame->title }}</div>
                                <span class="text-xs text-gray-500 block">Updated: {{ $lastLibraryGame->pivot->updated_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-500 mb-2">Your library is empty.</p>
                        <a href="{{ route('shop') }}" class="text-xs text-indigo-600 hover:underline">Browse Games</a>
                    @endif
                </div>
            @endauth

            <div>
                <a href="{{ route('projects.create') }}" class="block w-full text-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">+ Create Project</a>
            </div>
        </div>
    </div>

    <!-- 3. Active Community Projects -->
    <section>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900 mb-0">💡 Community Projects</h3>
            <a href="{{ route('projects') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">All Projects →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="flex flex-col justify-between bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div>
                        <h5 class="font-bold text-base text-gray-900 mb-2">{{ $project->title }}</h5>
                        <p class="text-xs text-gray-600 line-clamp-3">{{ Str::limit($project->content, 110) }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-cyan-50 text-cyan-700">⚙️ Mechanics: {{ $project->mechanics_count }}</span>
                        <a href="{{ route('projects.view', $project->id) }}" class="text-xs font-semibold px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">View</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full"><p class="text-gray-500 text-sm">No public projects found.</p></div>
            @endforelse
        </div>
    </section>
</div>
@endsection