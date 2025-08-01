@extends('layouts.app')

@push('styles')
<style>
.blog-content img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 1rem auto;
    border-radius: 6px;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card p-4">
                <h2 class="mb-3">{{ $blog->title }}</h2>

                <div class="text-muted mb-2">
                    By 
                    <strong>
                        <a href="{{ route('profile.view', ['id' => $blog->user->id]) }}">
                            {{ $blog->user->name }}
                        </a>
                    </strong> 
                    • Posted {{ $blog->created_at->diffForHumans() }}
                </div>

                <hr>

                <div class="blog-content">
                    {!! \Illuminate\Support\Str::markdown($blog->content) !!}
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary btn-sm">← To posts</a>
                    
                    @if(auth()->check() && (auth()->id() === $blog->user_id || auth()->user()->isAdmin()))
                        <div class="d-flex gap-2">
                            <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                            <a href="{{ route('blog.confirm-delete', $blog->id) }}" class="btn btn-outline-danger btn-sm">Delete</a>

                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>

</div>
<div class="row justify-content-center mt-4">
    <div class="col-md-8">

        <x-comment-section :object="$blog" type="blog" :comments="$comments" />

    </div>
</div>
@endsection
