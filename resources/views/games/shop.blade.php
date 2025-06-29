<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/style.css') }}">
    <title>Shop</title>
</head>
<body>
    <header>
        <x-navbar />
    </header>
    <main class="">
    <x-callback-message />

    @auth
        @if(Auth::user()->isAdmin())
            <a href="{{ route('games.create') }}" class="btn btn-success mt-3 ms-3">Add New Game</a>
        @endif
    @endauth
    <div class="mb-3"></div>
    {{-- list here --}}
    @foreach($games as $game)
    <div class="d-flex justify-content-center flex-wrap mb-5">
        <div class="w-25">
            <x-game-card :game="$game" />
        </div>
    </div>
    @endforeach

    <div class="d-flex flex-column align-items-center gap-2">
        {{-- {{ $games->links('pagination::bootstrap-5') }} --}}
        {{ $games->links('pagination::simple-bootstrap-5') }}
    </div>
    

    </main>

    <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
