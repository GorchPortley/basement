<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-base-100 text-base-content antialiased">
        @include('components.nav')

        @if (session('status'))
            <div class="max-w-6xl mx-auto px-4 pt-4">
                <div class="alert alert-success">{{ session('status') }}</div>
            </div>
        @endif

        {{ $slot }}
    </body>
</html>