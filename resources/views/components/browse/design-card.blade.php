{{--When the buttons get wired up, point them at the parent, e.g.
    wire:click="saveDesign({{ '{{ $design->id }}' }})".--}}
    
@props(['design'])

<div {{ $attributes->merge(['class' => 'card h-full border border-base-300 bg-base-100 shadow-sm']) }}>
    <figure class="px-3 pt-3">
        <x-browse.image-carousel
            :images="$design->getMedia('display_img')->map->getUrl()->all()"
            :alt="data_get($design->payload, 'meta.title', 'Design')"
            ratio="aspect-video"
        />
    </figure>

    <div class="card-body gap-2 p-4">
        <h2 class="card-title text-base">
            {{ data_get($design->payload, 'meta.title', 'Untitled design') }}
        </h2>

        <p class="text-sm opacity-70">by {{ $design->owner?->name ?? 'Unknown builder' }}</p>

        @if (filled($tagline = data_get($design->payload, 'meta.tagline')))
            <p class="text-sm opacity-70">{{ $tagline }}</p>
        @endif

        <div class="flex flex-wrap gap-1">
            <span class="badge badge-sm badge-outline">{{ $design->typeLabel() }}</span>

            @if (filled($cost = data_get($design->payload, 'meta.build_cost')))
                <span class="badge badge-sm badge-outline">${{ $cost }}</span>
            @endif

            <span class="badge badge-sm badge-outline">{{ $design->spec('Frequency Range') }}</span>
        </div>

        <div class="card-actions mt-2 justify-between">
            <x-button label="View Design" class="btn-sm btn-primary flex-1" />
            <x-button label="Save Design" class="btn-sm btn-outline flex-1" />
        </div>
    </div>
</div>
