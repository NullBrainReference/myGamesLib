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
                    By <strong>{{ $blog->user->name }}</strong> 
                    • Posted {{ $blog->created_at->diffForHumans() }}
                </div>

                <hr>

                <div class="blog-content">
                    {!! \Illuminate\Support\Str::markdown($blog->content) !!}
                </div>

                <div class="mt-4">
                    <a href="{{ route('shop') }}" class="btn btn-outline-secondary btn-sm">← Back to posts</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
