<div class="border-b border-base-300 bg-base-200">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
        <div role="tablist" class="tabs tabs-border">
            <a href="{{ route('admin.dashboard') }}"
               class="tab {{ request()->routeIs('admin.dashboard') ? 'tab-active' : '' }}">Overview</a>
            <a href="{{ route('admin.designs') }}"
               class="tab {{ request()->routeIs('admin.designs') ? 'tab-active' : '' }}">Designs</a>
            <a href="{{ route('admin.components') }}"
               class="tab {{ request()->routeIs('admin.components') ? 'tab-active' : '' }}">Components</a>
        </div>
        <span class="text-xs uppercase tracking-wide text-base-content/50">Admin</span>
    </div>
</div>
