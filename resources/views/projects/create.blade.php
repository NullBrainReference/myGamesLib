@extends('layouts.app')

@section('title', 'Initialize Project')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
    {{-- Navigation Back Link --}}
    <div class="mb-4">
        <a href="{{ route('projects') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
            ← Back to Blueprints
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-100 bg-white px-6 py-4">
            <h5 class="text-base font-bold text-gray-900">Launch New Project Blueprint</h5>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($commentId))
                    <input type="hidden" name="comment_id" value="{{ $commentId }}">
                @endif

                {{-- Project Title --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Project Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icons Upload Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Main Icon (Big)</label>
                        <input type="file" name="icon_big" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        @error('icon_big')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Secondary Icon (Small)</label>
                        <input type="file" name="icon_small" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        @error('icon_small')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Content Briefing --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Operational Content Briefing (Markdown Supported)</label>
                    <textarea name="content" rows="6" required
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Public Switch --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_public" id="isPublicSwitch" checked
                           class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                    <label for="isPublicSwitch" class="text-xs font-semibold text-gray-700">Make Project Space Public</label>
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('projects') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        Initialize Workspace
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
