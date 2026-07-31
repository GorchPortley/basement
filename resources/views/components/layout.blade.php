<!DOCTYPE html>
<html>
    <head>
        @include('partials.head')
    </head>
    <body>
        @include('components.navigation.nav')
        {{ $slot }}
        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>
