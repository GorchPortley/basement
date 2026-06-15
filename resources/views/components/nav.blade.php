<div class="navbar bg-primary">
    <div class="navbar-start">
    <a href=/ class="font-bold">SDLabs_Dev</a>
    </div>

    <div class="navbar-center">
        <a>center</a>
    </div>

    <div class="navbar-end">
        @guest
        <a href=/login><button class="btn bg-secondary btn-ghost m-1">Login</button></a>
        <a href=/register><button class="btn bg-secondary btn-ghost m-1">Register</button></a>
        @endguest

        @auth
        <a href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" class="hidden" action="/logout" method="POST">
            @csrf
        </form>
        @endauth
        <input type="checkbox" value="business" class="toggle theme-controller" />
    </div>    
</div>