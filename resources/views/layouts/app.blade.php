<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ url('css/style.css') }}">
    @stack('styles')
</head>
<body class="font-sans antialiased bg-light">

    <div class="d-flex flex-column min-vh-100">

        <header>
            <x-navbar />
        </header>

        <div id="app" class="d-flex flex-column flex-grow-1">
            <main class="flex-fill pb-5">
                @yield('content')
            </main>
        </div>

        <footer class="bg-dark text-secondary pt-4 mt-auto">
            <div class="container text-center pb-3">
                <p class="mb-0">&copy; {{ date('Y') }} MyGamesLib.</p>
            </div>

            <div class="bg-black text-muted py-2 text-center small border-top border-secondary">
                <span>
                    ● Active Users Online:
                    <strong class="text-white">
                        {{ \App\Singletones\UserSessionTracker::getInstance()->getActiveCount() }}
                    </strong>
                </span>
            </div>
        </footer>

    </div> <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')

    <script>
        function toggleTailwindReplyForm(event, commentId) {
            event.preventDefault();
            event.stopPropagation();

            var form = document.getElementById('replyForm-' + commentId);
            if (form) {
                // Tailwind uses the simple 'hidden' class to vanish items completely
                form.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
