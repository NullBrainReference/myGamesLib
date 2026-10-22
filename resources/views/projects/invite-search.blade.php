@extends('layouts.app')

@section('title', 'Invite Members - ' . $project->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">

    {{-- Header & Search Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('projects.view', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors mb-2">
                ← Back to {{ $project->title }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Invite Members to {{ $project->title }}</h1>
            <p class="text-xs text-gray-500 mt-1">Search community members to send project invitations.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('projects.invite.create', $project->id) }}" class="w-full md:w-80">
            <div class="relative flex items-center">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search by name or email..."
                       class="w-full pl-9 pr-10 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition shadow-sm">

                <svg class="w-4 h-4 text-gray-400 absolute left-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>

                @if($search)
                    <a href="{{ route('projects.invite.create', $project->id) }}" class="absolute right-3 text-gray-400 hover:text-gray-600 text-xs font-bold">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- System Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-3 bg-red-50 border border-red-200 text-red-800 text-xs font-medium rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search Results Banner --}}
    @if($search)
        <div class="mb-6 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex justify-between items-center text-xs text-indigo-900">
            <span>Search results for: <strong class="font-semibold">"{{ $search }}"</strong> ({{ $users->total() }} users found)</span>
            <a href="{{ route('projects.invite.create', $project->id) }}" class="text-indigo-600 font-semibold hover:underline">Reset search</a>
        </div>
    @endif

    {{-- Users Grid --}}
    @if($users->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($users as $user)
                @php
                    $isOwner = in_array($user->id, $ownerIds);
                    $isEditor = in_array($user->id, $editorIds);
                    $isParticipant = in_array($user->id, $participantIds);
                    $isPending = in_array($user->id, $pendingInviteUserIds);
                    $alreadyMember = $isOwner || $isEditor || $isParticipant;
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-base border border-indigo-200 shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            @if($isOwner)
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">
                                    👑 Owner
                                </span>
                            @elseif($isEditor)
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-sky-100 text-sky-800 border border-sky-300">
                                    ✏️ Editor
                                </span>
                            @elseif($isParticipant)
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-gray-100 text-gray-700 border border-gray-300">
                                    👤 Participant
                                </span>
                            @elseif($isPending)
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-100 text-purple-800 border border-purple-300">
                                    ⏳ Invited
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm font-bold text-gray-900 truncate mb-0.5">
                            <a href="{{ route('profile.view', $user->id) }}" class="hover:text-indigo-600 transition-colors">
                                {{ $user->name }}
                            </a>
                        </h3>
                        <p class="text-xs text-gray-400 truncate mb-4">{{ $user->email }}</p>
                    </div>

                    {{-- Invite Actions Form --}}
                    <div class="pt-3 border-t border-gray-100">
                        @if($alreadyMember)
                            <div class="w-full text-center py-1.5 px-3 bg-gray-50 text-gray-400 text-xs font-semibold rounded-lg border border-gray-200">
                                Already Member
                            </div>
                        @elseif($isPending)
                            <div class="w-full text-center py-1.5 px-3 bg-purple-50 text-purple-700 text-xs font-semibold rounded-lg border border-purple-200">
                                Invite Pending
                            </div>
                        @else
                            <form action="{{ route('projects.invite', $project->id) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="flex gap-1.5">
                                    <button type="submit" name="role" value="participant" class="w-1/2 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-lg shadow-sm transition-colors text-center">
                                        Participant
                                    </button>
                                    <button type="submit" name="role" value="editor" class="w-1/2 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-[11px] font-bold rounded-lg shadow-sm transition-colors text-center">
                                        Editor
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $users->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center my-8">
            <h3 class="text-sm font-bold text-gray-800">No users found</h3>
            <p class="text-xs text-gray-500 mt-1 mb-4">No community members matched your search criteria.</p>
            <a href="{{ route('projects.invite.create', $project->id) }}" class="inline-flex px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                Clear Search
            </a>
        </div>
    @endif
</div>
@endsection
