<div class="max-w-6xl mx-auto p-4 md:p-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold">Components</h1>
        @auth
            <a href="{{ route('studio.components.create') }}" class="btn btn-primary btn-sm">Add a component</a>
        @endauth
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-end gap-3 mb-6">
        <label class="input input-bordered flex items-center gap-2 flex-1 min-w-[200px]">
            <input type="search" wire:model.live.debounce.300ms="search" class="grow" placeholder="Search brand or model…" />
        </label>

        <select wire:model.live="category" class="select select-bordered">
            <option value="">All types</option>
            @foreach ($categories as $opt)
                <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
            @endforeach
        </select>

        <select wire:model.live="sort" class="select select-bordered">
            <option value="newest">Newest</option>
            <option value="brand">Brand A → Z</option>
        </select>

        <label class="label cursor-pointer gap-2">
            <span class="label-text">Verified only</span>
            <input type="checkbox" wire:model.live="official" class="toggle toggle-primary toggle-sm" />
        </label>

        <button wire:click="clearFilters" class="btn btn-ghost btn-sm">Reset</button>
    </div>

    {{-- Results --}}
    <div wire:loading.class="opacity-50" class="transition-opacity">
        @if ($components->isEmpty())
            <div class="text-center py-16 text-base-content/50">No components match your filters.</div>
        @else
            <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($components as $component)
                    <div wire:key="component-{{ $component->id }}">
                        <x-component-card :component="$component" />
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $components->links() }}</div>
        @endif
    </div>
</div>
