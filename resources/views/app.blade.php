<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>{{ config('app.name') }}</title>
    <meta name="description" content="Journal & Diary">

    <link rel="icon" href="/favicon.ico">
    <link rel="icon" href="/favicon.svg" sizes="any" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon-180x180.png" sizes="180x180">
    <meta name="theme-color" content="#fdf3e2">

    @production
        <link rel="manifest" href="/build/manifest.webmanifest">
    @endproduction

    @vite('resources/js/app/entrypoint.ts')
    @inertiaHead
</head>

<body class="antialiased">
    @inertia
</body>

</html>
