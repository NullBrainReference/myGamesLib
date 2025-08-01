@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-3">Are you sure you want to delete this post?</h4>
        <p class="mb-4"><strong>{{ $blog->title }}</strong></p>
        <div class="d-flex gap-3">
            <form action="{{ route('blog.delete', $blog->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Yes, delete</button>
            </form>
            <a href="{{ route('blog.view', $blog->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>
@endsection
