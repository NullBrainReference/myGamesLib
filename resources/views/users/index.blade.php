@extends('layouts.app')

@section('title', 'Community Members')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header & Search Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Community Members</h1>
            <p class="text-xs text-gray-500 mt-1">Find and connect with fellow creators and players.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('users.index') }}" class="w-full md:w-80">
            <div class="relative flex items-center">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search by name or email..."
                       class="w-full pl-9 pr-10 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition shadow-sm">

                {{-- Search Icon --}}
                <svg class="w-4 h-4 text-gray-400 absolute left-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>

                {{-- Clear Button --}}
                @if($search)
                    <a href="{{ route('users.index') }}" class="absolute right-3 text-gray-400 hover:text-gray-600 text-xs font-bold">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Search Results Banner --}}
    @if($search)
        <div class="mb-6 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex justify-between items-center text-xs text-indigo-900">
            <span>Search results for: <strong class="font-semibold">"{{ $search }}"</strong> ({{ $users->total() }} users found)</span>
            <a href="{{ route('users.index') }}" class="text-indigo-600 font-semibold hover:underline">Reset search</a>
        </div>
    @endif

    {{-- Users Grid --}}
    @if($users->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($users as $user)
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        {{-- Avatar / Role Badge --}}
                        <div class="flex items-start justify-between gap-2 mb-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-base border border-indigo-200 shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            @if($user->isAdmin())
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">
                                    Admin
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-gray-100 text-gray-600">
                                    Member
                                </span>
                            @endif
                        </div>

                        {{-- User Information --}}
                        <h3 class="text-sm font-bold text-gray-900 truncate mb-0.5">
                            <a href="{{ route('profile.view', $user->id) }}" class="hover:text-indigo-600 transition">
                                {{ $user->name }}
                            </a>
                        </h3>
                        <p class="text-xs text-gray-400 truncate mb-4">{{ $user->email }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('profile.view', $user->id) }}"
                           class="w-full text-center py-1.5 px-3 bg-gray-50 hover:bg-indigo-50 text-indigo-600 text-xs font-semibold rounded-lg border border-gray-200 hover:border-indigo-200 transition">
                            View Profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div>
            {{ $users->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center my-8">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="text-sm font-bold text-gray-800">No users found</h3>
            <p class="text-xs text-gray-500 mt-1 mb-4">No community members matched your search criteria.</p>
            <a href="{{ route('users.index') }}" class="inline-flex px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                Clear Filters
            </a>
        </div>
    @endif
</div>
@endsection
