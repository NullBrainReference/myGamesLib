@extends('layouts.app')

@section('title', 'Admin Dashboard - Posts')

@section('content')
<div class="container py-4">
    <x-callback-message />

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Review informational articles, monitor author assignments, and manage front-facing content.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-outline-secondary px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-primary active px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
        </div>
    </div>

    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-12 col-lg-8">
                <form method="GET" action="{{ route('dashboard.posts') }}">
                    <div class="row g-2">
                        <div class="col-12 col-sm-5">
                            <input type="text"
                                   name="title"
                                   value="{{ request('title') }}"
                                   class="form-control form-control-sm"
                                   placeholder="Filter by post title...">
                        </div>
                        <div class="col-12 col-sm-5">
                            <input type="text"
                                   name="author"
                                   value="{{ request('author') }}"
                                   class="form-control form-control-sm"
                                   placeholder="Filter by author name...">
                        </div>
                        <div class="col-12 col-sm-2 d-grid">
                            <button class="btn btn-sm btn-primary font-medium" type="submit">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-12 col-lg-4 d-flex align-items-center gap-3 justify-content-start justify-content-lg-end">
                @if(request('title') || request('author'))
                    <a href="{{ route('dashboard.posts') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none">Clear Filters</a>
                @endif
                <a href="{{ route('blog.create') }}" class="btn btn-sm btn-success font-medium shadow-sm px-3 d-inline-flex align-items-center">
                    <svg class="h-4 w-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Post
                </a>
            </div>
        </div>
    </div>

    @if($blogs->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold" style="width: 45%;">Post Title</th>
                            <th class="py-3 font-semibold">Author</th>
                            <th class="py-3 font-semibold">Published Date</th>
                            <th class="pe-4 py-3 text-end font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($blogs as $blog)
                            <tr>
                                <td class="ps-4 py-2.5">
                                    <a href="{{ route('blog.view', $blog->id) }}" class="text-decoration-none text-gray-900 font-semibold hover:text-blue-600 text-truncate d-inline-block" style="max-width: 400px;" title="{{ $blog->title }}">
                                        {{ $blog->title }}
                                    </a>
                                </td>

                                <td class="py-2.5">
                                    <a href="{{ route('profile.view', $blog->user->id) }}" class="text-decoration-none text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $blog->user->name }}
                                    </a>
                                </td>

                                <td class="py-2.5 text-gray-500 text-xs">
                                    {{ $blog->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td class="pe-4 py-2.5 text-end">
                                    <div class="d-inline-flex gap-1.5 justify-content-end align-items-center">
                                        <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-xs btn-outline-secondary font-medium px-2.5 py-1 shadow-sm rounded text-decoration-none text-xs">
                                            Edit
                                        </a>
                                        <a href="{{ route('blog.confirm-delete', $blog->id) }}" class="btn btn-xs btn-outline-danger font-medium px-2.5 py-1 shadow-sm rounded text-decoration-none text-xs">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 px-1">
            {{ $blogs->withQueryString()->links('pagination::simple-bootstrap-5') }}
        </div>
    @else
        <div class="card border border-gray-200 rounded-lg p-5 text-center bg-white shadow-sm">
            <div class="py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No matching articles found</h5>
                <p class="text-gray-500 small mb-3">No published editorial write-ups or postings align with your custom dashboard search constraints.</p>
                <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-secondary shadow-sm">Reset Content View</a>
            </div>
        </div>
    @endif
</div>
@endsection
