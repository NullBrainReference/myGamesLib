@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="container mb-5">

    @if(session('success'))
        <div class="alert alert-success text-center border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center mb-3 mt-3">
        <div class="col-lg-10">
            <a href="{{ route('projects') }}" class="btn btn-sm btn-link text-decoration-none text-muted p-0">
                <i class="bi bi-arrow-left"></i> Back to Blueprints Repository
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border border-gray-200 shadow-sm rounded-lg overflow-hidden bg-white">

                {{-- Control Toolbar Row --}}
                @auth
                    @if($project->owners->contains(Auth::id()))
                        <div class="bg-light border-bottom px-4 py-2 d-flex gap-2 justify-content-end">
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil-square"></i> Edit Properties
                            </a>
                            <form action="{{ route('projects.delete', $project->id) }}" method="POST" onsubmit="return confirm('Purge workspace node permanently?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i> Delete</button>
                            </form>
                        </div>
                    @endif
                @endauth

                {{-- Header Core Banner --}}
                <div class="card-body p-4 border-bottom bg-white d-flex align-items-center gap-4 flex-wrap flex-sm-nowrap">
                    <div class="flex-shrink-0 mx-auto mx-sm-0">
                        <div class="bg-light rounded-lg shadow-sm border d-flex align-items-center justify-content-center text-primary" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-boxes"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 text-center text-sm-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 mb-1">
                            <h2 class="h4 fw-bold text-gray-900 mb-0">{{ $project->title }}</h2>
                            <span class="badge {{ $project->is_public ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;">
                                {{ $project->is_public ? 'Public' : 'Private' }}
                            </span>
                        </div>
                        <p class="text-muted small mb-0">Project Space Node #{{ $project->id }} • Created {{ $project->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                {{-- Split Operational Grid --}}
                <div class="card-body p-4 row g-4">

                    {{-- Left Side: Briefing Text --}}
                    <div class="col-md-7 border-end border-gray-100 pe-md-4">
                        <h5 class="fw-bold text-gray-900 mb-3"><i class="bi bi-file-earmark-text text-muted"></i> Operational Briefing</h5>
                        <div class="text-gray-800" style="white-space: pre-wrap; line-height: 1.7;">{{ $project->content }}</div>
                    </div>

                    {{-- Right Side: Dynamic Access Directory Lookup Menus --}}
                    <div class="col-md-5 ps-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h5 class="fw-bold text-gray-900 mb-0"><i class="bi bi-shield-check text-muted"></i> Access Directory</h5>
                        </div>

                        {{-- Workspace Owners --}}
                        <div class="mb-4">
                            <h6 class="text-xs fw-bold text-uppercase text-muted tracking-wider mb-2">Workspace Leads / Owners</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($project->owners as $owner)
                                    <div class="small fw-semibold text-gray-800 d-flex align-items-center gap-2">
                                        <i class="bi bi-person-fill-gear text-warning"></i> {{ $owner->name }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Workspace Editors Lookup Group --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-xs fw-bold text-uppercase text-muted tracking-wider mb-0">Editors</h6>
                                @if(Auth::check() && $project->owners->contains(Auth::id()))
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0.5 px-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#manageEditorsModal" style="font-size: 0.7rem;">
                                        <i class="bi bi-gear-fill"></i> Manage
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex flex-column gap-1.5">
                                @forelse($project->editors as $editor)
                                    <div class="small text-gray-700 d-flex align-items-center gap-2">
                                        <i class="bi bi-pencil-square text-info"></i> {{ $editor->name }}
                                    </div>
                                @empty
                                    <div class="text-muted italic small">No assigned workspace editors.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Workspace Participants Lookup Group --}}
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-xs fw-bold text-uppercase text-muted tracking-wider mb-0">Participants ({{ $project->participants->count() }})</h6>
                                @if(Auth::check() && $project->owners->contains(Auth::id()))
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0.5 px-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#manageParticipantsModal" style="font-size: 0.7rem;">
                                        <i class="bi bi-gear-fill"></i> Manage
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex flex-column gap-1.5 max-h-48 overflow-y-auto">
                                @forelse($project->participants as $participant)
                                    <div class="small text-gray-600 d-flex align-items-center gap-2">
                                        <i class="bi bi-person text-secondary"></i> {{ $participant->name }}
                                    </div>
                                @empty
                                    <div class="text-muted italic small">No assigned space participants.</div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{--        LOOKUP DIALOG MODAL: EDITORS        --}}
{{-- ========================================== --}}
@if(Auth::check() && $project->owners->contains(Auth::id()))
<div class="modal fade" id="manageEditorsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-info"></i> Manage Project Editors</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Search Filters Input --}}
                <div class="input-group mb-3 shadow-sm rounded border">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="editorSearchInput" class="form-control border-0 ps-0" placeholder="Type name to lookup editor...">
                </div>

                <div class="list-group" id="editorListGroup">
                    @foreach($allUsers as $user)
                        @php $hasRole = $project->editors->contains($user->id); @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center editor-item-row" data-name="{{ strtolower($user->name) }}">
                            <div>
                                <span class="fw-bold d-block text-gray-900">{{ $user->name }}</span>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <div>
                                @if($hasRole)
                                    <form action="{{ route('projects.editors.detach', ['project_id' => $project->id, 'user_id' => $user->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger px-3 rounded-pill">Remove</button>
                                    </form>
                                @else
                                    <form action="{{ route('projects.editors.attach', $project->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-success px-3 rounded-pill">Add</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{--      LOOKUP DIALOG MODAL: PARTICIPANTS     --}}
{{-- ========================================== --}}
<div class="modal fade" id="manageParticipantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-people text-secondary"></i> Manage Project Participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Search Filters Input --}}
                <div class="input-group mb-3 shadow-sm rounded border">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="participantSearchInput" class="form-control border-0 ps-0" placeholder="Type name to lookup participant...">
                </div>

                <div class="list-group" id="participantListGroup">
                    @foreach($allUsers as $user)
                        @php $hasRole = $project->participants->contains($user->id); @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center participant-item-row" data-name="{{ strtolower($user->name) }}">
                            <div>
                                <span class="fw-bold d-block text-gray-900">{{ $user->name }}</span>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <div>
                                @if($hasRole)
                                    <form action="{{ route('projects.participants.detach', ['project_id' => $project->id, 'user_id' => $user->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger px-3 rounded-pill">Remove</button>
                                    </form>
                                @else
                                    <form action="{{ route('projects.participants.attach', $project->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-success px-3 rounded-pill">Add</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Client-Side JavaScript Live Filters --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup Editor Live Search Lookup
    const editorSearch = document.getElementById('editorSearchInput');
    const editorRows = document.querySelectorAll('.editor-item-row');

    editorSearch?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        editorRows.forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.setProperty('display', (!query || name.includes(query)) ? '' : 'none', 'important');
        });
    });

    // Setup Participant Live Search Lookup
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
@endsection
