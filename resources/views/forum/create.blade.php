@extends('layouts.app')

@section('title', 'Start New Discussion Thread')

@section('content')
<div class="container py-4 mb-5">
    <div class="row justify-content-center mb-3">
        <div class="col-lg-8 col-md-10">
            <a href="{{ route('forum') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-blue-600 transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Return to Forum Board
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="bg-gray-50 border-b border-gray-200 px-4 py-4 sm:px-6">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Initialize a New Forum Thread
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Share thoughts, open up development suggestions, or request support from the rest of the community.
                    </p>
                </div>

                <form action="{{ route('forum.threads.store') }}" method="POST" class="m-0">
                    @csrf

                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Thread Subject Title
                            </label>
                            <input type="text" name="title" id="title"
                                   value="{{ old('title') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2.5 text-sm @error('title') border-red-500 ring ring-red-100 @enderror"
                                   placeholder="Summarize your observation concisely..." required maxlength="255" autofocus>

                            @error('title')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="content" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Discussion Content Description
                            </label>
                            <textarea name="content" id="content" rows="8"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2.5 text-sm @error('body') border-red-500 ring ring-red-100 @enderror"
                                      placeholder="Elaborate on your topic thread. Provide references, architectural details, or background information..." required>{{ old('body') }}</textarea>

                            @error('content')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-center border-t border-gray-100">
                        <a href="{{ route('forum') }}"
                           class="px-4 py-2 bg-white border border-gray-300 rounded text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none decoration-none text-center">
                            Discard
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded text-xs font-semibold shadow-md hover:bg-blue-700 focus:outline-none flex items-center gap-1">
                            <i class="bi bi-send text-[10px]"></i> Broadcast Thread
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
