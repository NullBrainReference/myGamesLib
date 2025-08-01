@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Posts by {{ $author->name }}</h3>

    @forelse($blogs as $blog)
        <div class="card mb-3 p-3 shadow-sm">
            <h5>{{ $blog->title }}</h5>
            <div class="text-muted small mb-2">
                {{ $blog->created_at->diffForHumans() }}
            </div>
            <div class="text-truncate" style="max-height: 4.5em; overflow: hidden;">
                {!! \Illuminate\Support\Str::markdown(Str::limit($blog->content, 300)) !!}
            </div>
            <a href="{{ route('blog.view', $blog->id) }}" class="btn btn-outline-primary mt-2">Read More</a>
        </div>
    @empty
        <p>No posts by this author yet 💤</p>
    @endforelse

    <div class="mt-4">
        {{ $blogs->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection
