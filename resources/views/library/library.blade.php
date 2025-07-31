<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/style.css') }}">
    <title>My Library</title>
</head>
<body>
    <header>
        <x-navbar />
    </header>
    <main class="mt-3">

    @foreach($games as $game)
    <div class="d-flex justify-content-center flex-wrap mb-5">
        <div class="w-25">
            {{-- <x-game-card :game="$game" /> --}}
            <x-library-game-card :game="$game" :can-delete="$canDelete ?? false" />
        </div>
    </div>
    @endforeach

    </main>

    <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>