<a href="{{ route('game.view', ['id' => $game->game_id]) }}" class="text-decoration-none">
    <div class=" border rounded p-3">
        <!-- Well begun is half done. - Aristotle -->
        <img src="{{ $game->img_src }}" class="card-img-top" alt="{{ $game->title }}">
        <div class="card-body">
            <h5 class="card-title">{{ $game->title }}</h5>
            <p class="card-text">{{ $game->description }}</p>
        </div>
    </div>
</a>