@extends('layouts.app')

@section('title', 'Edit Project - ' . $project->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
    {{-- Navigation Back Link --}}
    <div class="mb-4">
        <a href="{{ route('projects.view', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
            ← Back to Blueprint
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-100 bg-white px-6 py-4 flex items-center justify-between">
            <h5 class="text-base font-bold text-gray-900">✏️ Edit Project Blueprint #{{ $project->id }}</h5>
            <span class="text-xs text-gray-400">Created: {{ $project->created_at->format('M d, Y') }}</span>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('projects.update', $project->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Project Title --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Project Title</label>
                    <input type="text" name="title" value="{{ old('title', $project->title) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icons Upload Fields & Current Previews --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Main Icon (Big) --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Main Icon (Big)</label>
                        @if($project->icon_big)
                            <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                <img src="{{ asset('storage/' . $project->icon_big) }}" alt="Big Icon" class="w-12 h-12 object-cover rounded-md border">
                                <span class="text-[11px] text-gray-500">Current Big Icon</span>
                            </div>
                        @endif
                        <input type="file" name="icon_big" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        @error('icon_big')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Secondary Icon (Small) --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Secondary Icon (Small)</label>
                        @if($project->icon_small)
                            <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                <img src="{{ asset('storage/' . $project->icon_small) }}" alt="Small Icon" class="w-8 h-8 object-cover rounded-md border">
                                <span class="text-[11px] text-gray-500">Current Small Icon</span>
                            </div>
                        @endif
                        <input type="file" name="icon_small" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        @error('icon_small')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Operational Content Briefing --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Operational Content Briefing (Markdown Supported)</label>
                    <textarea name="content" rows="7" required
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition @error('content') border-red-500 @enderror">{{ old('content', $project->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Public Workspace Switch --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_public" id="isPublicSwitch" {{ old('is_public', $project->is_public) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                    <label for="isPublicSwitch" class="text-xs font-semibold text-gray-700">Make Project Space Public</label>
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-[11px] text-gray-400">
                        💡 Team participants and editors sync automatically via <strong class="text-gray-600">Approve Project</strong>.
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('projects.view', $project->id) }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            Update Blueprint
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
