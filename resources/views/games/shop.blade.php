<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/style.css') }}">
    <title>Home</title>
</head>
<body>
    <header>
        <x-navbar />
    </header>
    <main class="">
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

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

    </main>

    <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
