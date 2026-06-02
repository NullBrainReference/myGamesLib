@extends('layouts.app')

@section('title', $thread->title)

@section('content')

{{-- System Status Alerts --}}
@if(session('success'))
    <div class="alert alert-success text-center shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Breadcrumb Navigation --}}
<div class="row justify-content-center mb-3">
    <div class="col-md-10 col-lg-8">
        <a href="{{ route('forum') }}" class="btn btn-sm btn-link text-decoration-none text-muted p-0">
            <i class="bi bi-arrow-left"></i> Back to Forum
        </a>
    </div>
</div>

{{-- Main Thread Component --}}
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">

            {{-- Thread Header Pane --}}
            <div class="card-body p-4 border-bottom bg-white">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    @if($thread->is_pinned)
                        <span class="badge bg-warning text-dark"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
                    @endif
                    @if($thread->is_locked)
                        <span class="badge bg-secondary"><i class="bi bi-lock-fill"></i> Locked</span>
                    @endif
                </div>

                <h2 class="h4 fw-bold text-gray-900 mb-3">{{ $thread->title }}</h2>

                {{-- Metadata Row --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-gray-700">
                            <i class="bi bi-person-circle"></i> {{ $thread->user->name }}
                        </span>
                        <span class="text-gray-400">•</span>
                        <span>Published {{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                    <div>
                        <span class="badge bg-light text-secondary border">
                            <i class="bi bi-chat-left-text"></i> {{ $comments->total() }} replies
                        </span>
                    </div>
                </div>
            </div>

            {{-- Thread Content Text Body --}}
            <div class="card-body p-4 bg-white">
                <div class="text-gray-800" style="white-space: pre-wrap; line-height: 1.6;">
                    {{ $thread->content }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Comments Loop Section --}}
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-10 col-lg-8">
        @if($thread->is_locked)
            <div class="alert alert-secondary text-center border-0 shadow-sm">
                <i class="bi bi-lock-fill"></i> This discussion has been locked by a moderator.
            </div>
        @endif

        {{-- Injecting your existing polymorphic comment section layout --}}
        <x-comment-section :object="$thread" type="thread" :comments="$comments" />
    </div>
</div>

@endsection
