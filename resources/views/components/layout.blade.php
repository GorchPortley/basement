<!DOCTYPE html>
<html>
    <head>
        @include('partials.head')
    </head>
    <body>
        @include('components.nav')
        {{ $slot }}
    </body>
</html>