@extends('layouts.app')

@section('content')
<div class="container py-4">

    <x-callback-message />

    <form method="GET" action="{{ route('blog.index') }}" class="d-flex mb-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search blogs by title...">
        <button type="submit" class="btn btn-outline-primary">Search</button>
    </form>

    <h3 class="mb-4">📚 All Posts</h3>

    @auth
        <a href="{{ route('blog.create') }}" class="btn btn-success mb-2">
            Create Post
        </a>
    @endauth

    @forelse($blogs as $blog)
        <div class="card mb-3 p-3 shadow-sm">
            <h5>{{ $blog->title }}</h5>
            <div class="text-muted small mb-2">
                By 
                <strong>
                    <a href="{{ route('profile.view', ['id' => $blog->user->id]) }}">
                        {{ $blog->user->name }}
                    </a>
                </strong> • {{ $blog->created_at->diffForHumans() }}
            </div>
            <div class="text-truncate" style="max-height: 4.5em; overflow: hidden;">
                {!! \Illuminate\Support\Str::markdown(Str::limit($blog->content, 300)) !!}
            </div>
            <a href="{{ route('blog.view', $blog->id) }}" class="btn btn-outline-primary mt-2">Read More</a>
        </div>
    @empty
        <p>No blog posts yet ✍️</p>
    @endforelse

    <div class="mt-4">
        {{ $blogs->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection
