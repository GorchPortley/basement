{{-- Global top navigation. Pure daisyUI (navbar + dropdown + menu).
     Theme is controlled by the daisyUI theme-controller toggle (wireframe <-> business). --}}
<div class="navbar bg-base-200 border-b border-base-300">
    <div class="navbar-start">
        <a href="{{ route('home') }}" class="btn btn-ghost text-lg font-semibold">SDLabs</a>
    </div>

    <div class="navbar-center hidden md:flex">
        <a href="{{ route('designs.index') }}" class="btn btn-ghost">Designs</a>
        <a href="{{ route('components.index') }}" class="btn btn-ghost">Components</a>
        @if (config('services.forum.url'))
            <a href="{{ config('services.forum.url') }}" class="btn btn-ghost">Forum</a>
        @endif
    </div>

    <div class="navbar-end gap-2">
        {{-- Theme toggle: checked = "business" (dark), unchecked = default "wireframe" --}}
        <label class="swap swap-rotate" title="Toggle theme">
            <input type="checkbox" value="business" class="theme-controller" />
            <span class="swap-off text-xs">Light</span>
            <span class="swap-on text-xs">Dark</span>
        </label>

        @guest
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">Account</div>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-48 p-2 shadow border border-base-300">
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                </ul>
            </div>
        @endguest

        @auth
            <a href="{{ route('studio.designs.create') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Publish</a>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">{{ auth()->user()->name }}</div>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-56 p-2 shadow border border-base-300">
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('studio.designs.create') }}">New design</a></li>
                    <li><a href="{{ route('studio.components.create') }}">New component</a></li>
                    @if (auth()->user()->isAdmin())
                        <li class="menu-title">Admin</li>
                        <li><a href="{{ route('admin.dashboard') }}">Admin dashboard</a></li>
                    @endif
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </li>
                </ul>
            </div>
            <form id="logout-form" class="hidden" action="/logout" method="POST">@csrf</form>
        @endauth
    </div>
</div>
