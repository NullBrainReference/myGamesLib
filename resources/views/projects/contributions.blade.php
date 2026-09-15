@extends('layouts.app')

@section('title', 'Contributions - ' . $project->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mb-12">
    <div class="mb-4">
        <a href="{{ route('projects.view', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
            ← Back to Project Blueprint
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-1">💬 Project Contribution Tree</h2>
        <p class="text-xs text-gray-500">
            Discussion thread and historical feedback originating from project node: <strong class="text-gray-800">{{ $project->title }}</strong>
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        @if($project->comment)
            <div class="border-l-4 border-indigo-500 pl-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-700">🌱 Origin Contribution</span>
                    <span class="text-[10px] text-gray-400">{{ $project->comment->created_at?->diffForHumans() }}</span>
                </div>
                <div class="text-xs font-semibold text-gray-800">By: {{ $project->comment->user->name ?? 'Anonymous' }}</div>
                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                    {{ $project->comment->content ?? $project->comment->body }}
                </p>
            </div>

            @if($project->comment->replies && $project->comment->replies->count() > 0)
                <div class="pl-6 space-y-4 border-l border-gray-200 ml-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Discussion Branches ({{ $project->comment->replies->count() }})</h4>

                    @foreach($project->comment->replies as $reply)
                        <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm space-y-1">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-gray-800">{{ $reply->user->name ?? 'User' }}</span>
                                <span class="text-gray-400 text-[10px]">{{ $reply->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-600">{{ $reply->content ?? $reply->body }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 italic">No further replies in this contribution branch yet.</p>
            @endif
        @else
            <div class="text-center py-8 text-gray-400 text-xs">
                No origin comment thread linked to this project workspace.
            </div>
        @endif
    </div>
</div>
@endsection
