@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if ($errors->has('image'))
        <div class="alert alert-danger">
            {{ $errors->first('image') }}
        </div>
    @endif
    <h3 class="mb-4">Create Blog Post</h3>

    {{-- Image Upload --}}
    <div class="card mb-4 p-4">
        <h5>Upload Image</h5>
        <form action="{{ route('image.upload-temp') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="title" value="{{ old('title') }}">
            <input type="hidden" name="content" value="{{ old('content') }}">

            <div class="mb-3">
                <label for="image" class="form-label">Choose image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        {{-- Show pending images preview --}}
        @if(session('pending_images'))
            <div class="mt-3">
                <h6>🖼️ Uploaded Images</h6>
                <div class="row">
                    @foreach(session('pending_images') as $imgPath)
                        <div class="col-md-4 mb-3">
                            <img src="{{ asset($imgPath) }}" alt="Preview" class="img-fluid rounded shadow border">
                            <pre class="bg-light p-2 mt-2"><code>![Alt text]({{ asset($imgPath) }})</code></pre>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Markdown-based Blog Post Form --}}
    <div class="card p-4">
        <h5>Write Post (Markdown supported)</h5>
        <form action="{{ route('blog.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    required
                >
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content (Markdown)</label>
                <textarea 
                    class="form-control" 
                    id="content" 
                    name="content" 
                    rows="12" 
                    placeholder="Write your content here using Markdown..."
                >{{ old('content') }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Publish</button>
        </form>
    </div>
</div>
@endsection
