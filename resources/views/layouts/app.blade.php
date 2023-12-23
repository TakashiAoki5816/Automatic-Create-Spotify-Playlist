<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Automatic Create Spotify Playlist</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Favicon --}}
    <link rel="icon" href="https://developer.spotify.com/images/guidelines/design/icon3@2x.png">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
    @vite('resources/js/app.ts')
</head>

<body>
    <div>
        @yield('content')
    </div>
</body>

</html>