@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mb-12" x-data="{ editorModal: false, participantModal: false }">

    @if(session('success'))
        <div class="mb-4 p-4 text-center rounded-lg bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('projects') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
            ← Back to Blueprints Repository
        </a>

        @if($project->comment_id)
            <a href="{{ route('projects.contributions', $project->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition">
                💬 Contributions & Thread Tree →
            </a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Control Toolbar Row --}}
        @auth
            @if($project->owners->contains(Auth::id()))
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-2.5 flex gap-2 justify-end">
                    <a href="{{ route('projects.edit', $project->id) }}" class="px-3 py-1 border border-amber-300 text-amber-700 hover:bg-amber-50 rounded-md text-xs font-semibold transition">
                        ✏️ Edit Properties
                    </a>
                    <form action="{{ route('projects.delete', $project->id) }}" method="POST" onsubmit="return confirm('Purge workspace node permanently?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1 border border-red-300 text-red-600 hover:bg-red-50 rounded-md text-xs font-semibold transition">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        {{-- Header Core Banner --}}
        <div class="p-6 border-b border-gray-200 bg-white flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
            <div class="flex-shrink-0">
                @if($project->icon_big)
                    <img src="{{ asset('storage/' . $project->icon_big) }}" alt="{{ $project->title }}" class="w-20 h-20 rounded-xl shadow-sm border border-gray-200 object-cover">
                @else
                    <div class="w-20 h-20 rounded-xl shadow-sm border border-gray-200 bg-indigo-50 flex items-center justify-center text-indigo-600 text-3xl">
                        📦
                    </div>
                @endif
            </div>
            <div class="flex-grow">
                <div class="flex items-center justify-center sm:justify-start gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $project->title }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $project->is_public ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $project->is_public ? 'Public' : 'Private' }}
                    </span>
                </div>
                <p class="text-xs text-gray-500">Project Space Node #{{ $project->id }} • Created {{ $project->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Split Operational Grid --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-8">

            {{-- Left Side: Briefing Text + Mechanics --}}
            <div class="md:col-span-7 space-y-6 md:border-r md:border-gray-100 md:pr-6">
                <div>
                    <h5 class="font-bold text-sm text-gray-900 mb-3 flex items-center gap-2">
                        📄 Operational Briefing
                    </h5>
                    <div class="text-gray-700 text-sm leading-relaxed space-y-3 prose max-w-none">
                        {!! Parsedown::instance()->text($project->content) !!}
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h5 class="font-bold text-sm text-gray-900 mb-3 flex items-center gap-2">
                        ⚙️ Project Mechanics
                    </h5>
                    @if($project->mechanics && $project->mechanics->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($project->mechanics as $mechanic)
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-cyan-50 text-cyan-800 border border-cyan-200">
                                    ⚙️ {{ $mechanic->title ?? $mechanic->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">No game mechanics linked to this project yet.</p>
                    @endif
                </div>
            </div>

            {{-- Right Side: Access Directory --}}
            <div class="md:col-span-5 space-y-6">
                <div class="border-b border-gray-100 pb-2">
                    <h5 class="font-bold text-sm text-gray-900">🛡️ Access Directory</h5>
                </div>

                {{-- Workspace Owners --}}
                <div>
                    <h6 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Workspace Leads / Owners</h6>
                    <div class="space-y-2">
                        @foreach($project->owners as $owner)
                            <div class="text-xs font-semibold text-gray-800 flex items-center gap-2">
                                <span class="text-amber-500">👑</span> {{ $owner->name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Workspace Editors --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h6 class="text-xs font-bold uppercase tracking-wider text-gray-400">Editors</h6>
                        @if(Auth::check() && $project->owners->contains(Auth::id()))
                            <button @click="editorModal = true" class="px-2 py-0.5 rounded-full border border-indigo-200 text-indigo-600 text-[10px] font-semibold hover:bg-indigo-50 transition">
                                ⚙️ Manage
                            </button>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        @forelse($project->editors as $editor)
                            <div class="text-xs text-gray-700 flex items-center gap-2">
                                <span class="text-sky-500">✏️</span> {{ $editor->name }}
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 italic">No assigned workspace editors.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Workspace Participants --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h6 class="text-xs font-bold uppercase tracking-wider text-gray-400">Participants ({{ $project->participants->count() }})</h6>
                        @if(Auth::check() && $project->owners->contains(Auth::id()))
                            <button @click="participantModal = true" class="px-2 py-0.5 rounded-full border border-indigo-200 text-indigo-600 text-[10px] font-semibold hover:bg-indigo-50 transition">
                                ⚙️ Manage
                            </button>
                        @endif
                    </div>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto">
                        @forelse($project->participants as $participant)
                            <div class="text-xs text-gray-600 flex items-center gap-2">
                                <span class="text-gray-400">👤</span> {{ $participant->name }}
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 italic">No assigned space participants.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MANAGE EDITORS MODAL --}}
    @if(Auth::check() && $project->owners->contains(Auth::id()))
    <div x-show="editorModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-gray-900 text-sm">✏️ Manage Project Editors</h3>
                <button @click="editorModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($allUsers as $user)
                    @php $hasRole = $project->editors->contains($user->id); @endphp
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div>
                            <span class="text-xs font-bold text-gray-800 block">{{ $user->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $user->email }}</span>
                        </div>
                        @if($hasRole)
                            <form action="{{ route('projects.editors.detach', ['project_id' => $project->id, 'user_id' => $user->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded-full text-xs font-semibold hover:bg-red-700 transition">Remove</button>
                            </form>
                        @else
                            <form action="{{ route('projects.editors.attach', $project->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="px-2.5 py-1 border border-emerald-600 text-emerald-600 rounded-full text-xs font-semibold hover:bg-emerald-50 transition">Add</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MANAGE PARTICIPANTS MODAL --}}
    <div x-show="participantModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-gray-900 text-sm">👥 Manage Project Participants</h3>
                <button @click="participantModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($allUsers as $user)
                    @php $hasRole = $project->participants->contains($user->id); @endphp
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div>
                            <span class="text-xs font-bold text-gray-800 block">{{ $user->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $user->email }}</span>
                        </div>
                        @if($hasRole)
                            <form action="{{ route('projects.participants.detach', ['project_id' => $project->id, 'user_id' => $user->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded-full text-xs font-semibold hover:bg-red-700 transition">Remove</button>
                            </form>
                        @else
                            <form action="{{ route('projects.participants.attach', $project->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="px-2.5 py-1 border border-emerald-600 text-emerald-600 rounded-full text-xs font-semibold hover:bg-emerald-50 transition">Add</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editorSearch = document.getElementById('editorSearchInput');
    const editorRows = document.querySelectorAll('.editor-item-row');

    editorSearch?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        editorRows.forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.setProperty('display', (!query || name.includes(query)) ? '' : 'none', 'important');
        });
    });

    const participantSearch = document.getElementById('participantSearchInput');
    const participantRows = document.querySelectorAll('.participant-item-row');

    participantSearch?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        participantRows.forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.setProperty('display', (!query || name.includes(query)) ? '' : 'none', 'important');
        });
    });
});
</script>
