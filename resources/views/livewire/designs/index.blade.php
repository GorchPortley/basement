<div class="max-w-6xl mx-auto p-4 md:p-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold">Designs</h1>
        @auth
            <a href="{{ route('studio.designs.create') }}" class="btn btn-primary btn-sm">Publish a design</a>
        @endauth
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-end gap-3 mb-6">
        <label class="input input-bordered flex items-center gap-2 flex-1 min-w-[200px]">
            <input type="search" wire:model.live.debounce.300ms="search" class="grow" placeholder="Search designs…" />
        </label>

        <select wire:model.live="category" class="select select-bordered">
            <option value="">All categories</option>
            @foreach ($categories as $opt)
                <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
            @endforeach
        </select>

        <select wire:model.live="sort" class="select select-bordered">
            <option value="newest">Newest</option>
            <option value="price_low">Price: low → high</option>
            <option value="price_high">Price: high → low</option>
        </select>

        <label class="label cursor-pointer gap-2">
            <span class="label-text">Official only</span>
            <input type="checkbox" wire:model.live="official" class="toggle toggle-primary toggle-sm" />
        </label>

        <button wire:click="clearFilters" class="btn btn-ghost btn-sm">Reset</button>
    </div>

    {{-- Results --}}
    <div wire:loading.class="opacity-50" class="transition-opacity">
        @if ($designs->isEmpty())
            <div class="text-center py-16 text-base-content/50">No designs match your filters.</div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($designs as $design)
                    <div wire:key="design-{{ $design->id }}">
                        <x-design-card :design="$design" />
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $designs->links() }}</div>
        @endif
    </div>
</div>
