@extends('layouts.app')

@section('title', 'Admin Dashboard - Comments')

@section('content')
<div class="container py-4">
    <x-callback-message />

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Audit user-generated remarks, track content sources, and manage moderation queues.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-primary active px-3">Comments</a>
        </div>
    </div>

    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <form method="GET" action="{{ route('dashboard.comments') }}">
            <div class="row g-2">
                <div class="col-12 col-sm-6 col-md-3">
                    <input type="text"
                           name="comment"
                           value="{{ request('comment') }}"
                           class="form-control form-control-sm"
                           placeholder="Filter by comment text...">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <input type="text"
                           name="object"
                           value="{{ request('object') }}"
                           class="form-control form-control-sm"
                           placeholder="Filter by content title...">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <input type="text"
                           name="user"
                           value="{{ request('user') }}"
                           class="form-control form-control-sm"
                           placeholder="Filter by username...">
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-primary flex-grow-1 font-medium px-3" type="submit">Filter Search</button>
                    @if(request('comment') || request('object') || request('user'))
                        <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center px-2.5" title="Clear All Filters">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($comments->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold" style="width: 22%;">Target Content</th>
                            <th class="py-3 font-semibold" style="width: 18%;">Author</th>
                            <th class="py-3 font-semibold" style="width: 40%;">Comment Snippet</th>
                            <th class="py-3 font-semibold">Posted Date</th>
                            <th class="pe-4 py-3 text-end font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($comments as $comment)
                            <tr>
                                <td class="ps-4 py-2.5">
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        @if($comment->commentable instanceof \App\Models\Game)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 rounded text-xs font-semibold">Game</span>
                                            <a href="{{ route('game.view', $comment->commentable->game_id) }}" class="text-decoration-none text-gray-900 font-medium text-truncate d-inline-block style-title-link" style="max-width: 180px;" title="{{ $comment->commentable->title }}">
                                                {{ $comment->commentable->title }}
                                            </a>
                                        @elseif($comment->commentable instanceof \App\Models\Blog)
                                            <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-0.5 rounded text-xs font-semibold">Blog</span>
                                            <a href="{{ route('blog.view', $comment->commentable->id) }}" class="text-decoration-none text-gray-900 font-medium text-truncate d-inline-block style-title-link" style="max-width: 180px;" title="{{ $comment->commentable->title }}">
                                                {{ $comment->commentable->title }}
                                            </a>
                                        @elseif($comment->commentable instanceof \App\Models\Thread)
                                            <span class="badge bg-purple-300-subtle text-purple border border-purple-subtle px-2 py-0.5 rounded text-xs font-semibold">Thread</span>
                                            <a href="{{ route('forum.thread', $comment->commentable->id) }}" class="text-decoration-none text-gray-900 font-medium text-truncate d-inline-block style-title-link" style="max-width: 180px;" title="{{ $comment->commentable->title }}">
                                                {{ $comment->commentable->title }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-gray-400 border border-gray-200 px-2 py-0.5 rounded text-xs">Missing Object</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-2.5">
                                    <a href="{{ route('profile.view', $comment->user->id) }}" class="text-decoration-none text-blue-600 hover:text-blue-800 font-semibold">
                                        {{ $comment->user->name }}
                                    </a>
                                </td>

                                <td class="py-2.5 text-gray-600 small">
                                    <span title="{{ $comment->content }}">
                                        {{ Str::limit($comment->content, 85) }}
                                    </span>
                                </td>

                                <td class="py-2.5 text-gray-500 text-xs">
                                    {{ $comment->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td class="pe-4 py-2.5 text-end">
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Permanently discard this user comment? This operation cannot be reversed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger font-medium px-2.5 py-1 shadow-sm rounded text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 px-1">
            {{ $comments->withQueryString()->links('pagination::simple-bootstrap-5') }}
        </div>
    @else
        <div class="card border border-gray-200 rounded-lg p-5 text-center bg-white shadow-sm">
            <div class="py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No matching comments found</h5>
                <p class="text-gray-500 small mb-3">No system commentary modifications or message data match your active search constraints.</p>
                <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-secondary shadow-sm">Reset Moderation Feed</a>
            </div>
        </div>
    @endif
</div>
@endsection
