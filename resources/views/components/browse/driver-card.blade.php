{{--
    One driver in the browse grid.

    This is a plain Blade component, not a Livewire one: it has no state of its
    own, so it renders inside whichever Livewire component lists it. When the
    buttons get wired up, point them at the parent, e.g.
    wire:click="saveDriver({{ '{{ $driver->id }}' }})".

    Usage: <x-browse.driver-card :driver="$driver" />
--}}
@props(['driver'])

<div {{ $attributes->merge(['class' => 'card h-full border border-base-300 bg-base-100 shadow-sm']) }}>
    <figure class="px-3 pt-3">
        <x-browse.image-carousel
            :images="$driver->getMedia('prod_img')->map->getUrl()->all()"
            :alt="$driver->displayName()"
        />
    </figure>

    <div class="card-body gap-2 p-4">
        <h2 class="card-title text-base">{{ $driver->displayName() }}</h2>

        <div class="flex flex-wrap gap-1">
            <span class="badge badge-sm badge-outline">{{ $driver->typeLabel() }}</span>

            @if (filled($size = data_get($driver->payload, 'meta.size')))
                <span class="badge badge-sm badge-outline">{{ $size }}"</span>
            @endif

            @if (filled($impedance = data_get($driver->payload, 'meta.impedance')))
                <span class="badge badge-sm badge-outline">{{ $impedance }} &Omega;</span>
            @endif

            @if (filled($power = data_get($driver->payload, 'specs.tsparam.Pe')))
                <span class="badge badge-sm badge-outline">{{ $power }}</span>
            @endif
        </div>

        @if (filled($tag = data_get($driver->payload, 'meta.tag')))
            <p class="text-sm opacity-70">{{ $tag }}</p>
        @endif

        <div class="card-actions mt-2 justify-between">
            <x-button label="View Driver" class="btn-sm btn-primary flex-1" />
            <x-button label="Save Driver" class="btn-sm btn-outline flex-1" />
        </div>
    </div>
</div>
