<div class="navbar bg-primary">
    <div class="navbar-start">
    <a href=/ >SDLabs_Dev</a>
    </div>

    <div class="navbar-center">
        <a href="{{ route('designs') }}"><button class="btn btn-ghost">Designs</button></a>
        <a href="{{ route('drivers') }}"><button class="btn btn-ghost">Drivers</button></a>
        <a><button class="btn btn-ghost">Blog</button></a>
        <a href="{{ config('services.forum.url') }}" ><button class="btn btn-ghost">Forum</button></a>
    </div>

    <div class="navbar-end">
        @guest
        <x-dropdown no-x-anchor right class="m-2">
        <x-menu-item title="Login" href=/login />
        <x-menu-item title="Register" href=/register />
                <input type="checkbox" value="business" class="toggle theme-controller" />
        </x-dropdown>
        @endguest

        @auth
        <x-dropdown no-x-anchor right class="m-2">
        <x-menu-item title="Dashboard" href="{{ route('dashboard') }}" />
        <x-menu-item title="Logout" href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" />
        <form id="logout-form" class="hidden" action="/logout" method="POST">
            @csrf
        </form>
                <input type="checkbox" value="business" class="toggle theme-controller" />
        </x-dropdown>
        @endauth

    </div>    
</div>