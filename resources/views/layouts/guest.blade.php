<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Flonewell Water') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 px-4 sm:px-6 lg:px-8 text-white">
            <div class="absolute top-0 left-0 w-full h-64 bg-blue-600 transform -skew-y-6 z-0 opacity-5"></div>
            <div class="w-full sm:max-w-md relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
