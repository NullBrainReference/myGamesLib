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
    <main>
    {{-- list here --}}
    @foreach($games as $game)
    <div class="d-flex justify-content-center flex-wrap">
        <x-game-card :game="$game" />
    </div>
    @endforeach

    </main>

    <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
