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
        <x-callback-message />
    </header>
    <main class="mt-3">
    @if($latestGame)
        <div class="d-flex justify-content-center flex-wrap mb-5">
            <div class="w-25">
                <x-game-card :game="$latestGame" />
            </div>
        </div>
    @else
        <p class="text-center">No games available.</p>
    @endif

    </main>

    <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
