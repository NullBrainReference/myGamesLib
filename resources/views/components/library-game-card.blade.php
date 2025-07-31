<div class="position-relative">
    <a href="{{ route('game.view', ['id' => $game->game_id]) }}" class="text-decoration-none">
        <div class="border rounded p-3">
            <img src="{{ asset($game->img_src) }}" class="card-img-top" alt="{{ $game->title }}">
            <div class="card-body">
                <h5 class="card-title">{{ $game->title }}</h5>
                <p class="card-text">{{ $game->description }}</p>
            </div>
        </div>
    </a>
    {{-- @if(isset($canDelete) && $canDelete)
        <form action="{{ route('library.remove', $game->game_id) }}" method="POST" class="position-absolute" style="top:10px; right:10px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" title="Remove from lib">&times;</button>
        </form>
    @endif --}}
</div>