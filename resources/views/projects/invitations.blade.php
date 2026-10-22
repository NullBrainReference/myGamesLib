@extends('layouts.app')

@section('title', 'Project Invitations')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Project Invitations</h1>
            <p class="text-xs text-gray-500">Manage pending and historical project membership invites.</p>
        </div>
        <a href="{{ route('projects') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors">
            ← Back to Projects
        </a>
    </div>

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

    @if($invitations->count() > 0)
        <div class="space-y-4">
            @foreach($invitations as $invitation)
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold px-2 py-0.5 rounded {{ $invitation->role === 'editor' ? 'bg-sky-100 text-sky-800' : 'bg-gray-100 text-gray-800' }}">
                                Role: {{ ucfirst($invitation->role) }}
                            </span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $invitation->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($invitation->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($invitation->status) }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">{{ $invitation->project->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Invited by <span class="font-semibold text-gray-700">{{ $invitation->inviter->name }}</span> • {{ $invitation->created_at->diffForHumans() }}
                        </p>
                    </div>

                    @if($invitation->status === 'pending')
                        <div class="flex items-center gap-2 shrink-0">
                            <form action="{{ route('projects.invitations.accept', $invitation->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                                    Accept & Join
                                </button>
                            </form>
                            <form action="{{ route('projects.invitations.decline', $invitation->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold rounded-lg transition-colors">
                                    Decline
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('projects.view', $invitation->project->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline shrink-0">
                            View Project →
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $invitations->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-sm">
            <h3 class="text-sm font-bold text-gray-800">No project invitations</h3>
            <p class="text-xs text-gray-500 mt-1">You have no pending or processed invitations.</p>
        </div>
    @endif
</div>
@endsection
