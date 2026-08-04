<div class="navbar h-18 bg-primary text-primary-content">
    <div class="navbar-start">
    <a href=/ >SDLabs_Dev</a>
    </div>

    <div class="h-full navbar-center">
        <x-mary-button link="{{ route('designs') }}" label="Designs" class="btn-ghost rounded-none bg-primary h-full" />
        <x-mary-button link="{{route('drivers')}}" label="Drivers" class="btn-ghost rounded-none bg-primary h-full" />
        <x-mary-button link="{{ route('blog') }}" label="Blog" class="btn-ghost rounded-none bg-primary h-full" />
        <x-mary-button link="{{config('services.forum.url')}}" label="Forum" class="btn-ghost rounded-none bg-primary h-full" />
    </div>

    <div class="navbar-end">
        @guest
        <x-dropdown no-x-anchor right class="m-2">
        <x-menu-item title="Login" href="{{ route('login') }}" />
        <x-menu-item title="Register" href="{{ route('register') }}" />
        </x-dropdown>
        @endguest

        @auth
        <x-dropdown no-x-anchor right class="m-2">
        <x-menu-item title="Dashboard" href="{{ route('dashboard') }}" class="" />
        <x-menu-item title="Logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class=""/>
        <form id="logout-form" class="hidden" action="{{ route('logout') }}" method="POST" class="">
            @csrf
        </form>
        </x-dropdown>
        @endauth

    </div>
</div>
