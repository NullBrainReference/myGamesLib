@extends('layouts.app')

@section('content')
<div class="container">
    <x-callback-message />

    <div class="d-flex align-items-baseline gap-2 mb-3">
        <h1 class="mb-0">Posts</h1>
        <a href="{{ route('dashboard.users') }}" class="text-decoration-none lh-1 pt-1">to users</a>
        <a href="{{ route('dashboard.games') }}" class="text-decoration-none lh-1 pt-1">to games</a>
        <a href="{{ route('dashboard.comments') }}" class="text-decoration-none lh-1 pt-1">to comments</a>
    </div>

    <form method="GET" action="{{ route('dashboard.posts') }}" class="mb-3 row g-2">
        <div class="col-md-6">
            <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="Post title">
        </div>
        <div class="col-md-6">
            <input type="text" name="author" value="{{ request('author') }}" class="form-control" placeholder="Author name">
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    <a href="{{ route('blog.create') }}" class="btn btn-success mb-2">
        Create Post
    </a>

    @if($blogs->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blogs as $blog)
                    <tr>
                        <td>
                            <a href="{{ route('blog.view', $blog->id) }}">{{ $blog->title }}</a>
                        </td>
                        <td>
                            <a href="{{ route('profile.view', $blog->user->id) }}">
                                {{ $blog->user->name }}
                            </a>
                        </td>
                        <td>{{ $blog->created_at->format('Y-m-d H:i') }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-sm btn-outline-secondary">
                                Edit
                            </a>

                            <a href="{{ route('blog.confirm-delete', $blog->id) }}" class="btn btn-sm btn-outline-danger">
                                Delete
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        {{ $blogs->withQueryString()->links('pagination::simple-bootstrap-5') }}
    @else
        <p>No posts found.</p>
    @endif
</div>
@endsection
