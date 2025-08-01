<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->
        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ url('css/style.css') }}"> -->
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <!-- <div class="min-h-screen bg-gray-100"> -->

            <header>
                <x-navbar />
            </header>

            <main>
                @yield('content')
            </main>
            <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
        <!-- </div> -->
    </body>
</html>
