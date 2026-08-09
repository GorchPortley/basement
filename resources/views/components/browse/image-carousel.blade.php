{{--
    A small image carousel for the browse cards.

    Pass it a plain array of image URLs. The first image is rendered by the
    server so the card looks right before Alpine boots; Alpine then swaps the
    src when the arrows are clicked. Arrows only appear if there is more than
    one image.

    Usage: <x-browse.image-carousel :images="$urls" alt="Some speaker" />
--}}
@props([
    'images' => [],
    'alt' => '',
    'ratio' => 'aspect-square',
])

@php
    $images = array_values(array_filter($images));
@endphp

@if (empty($images))
    <div class="{{ $ratio }} w-full rounded-box bg-base-200 flex items-center justify-center">
        <span class="text-sm opacity-60">No image</span>
    </div>
@else
    <div
        x-data="{ index: 0, images: @js($images) }"
        class="{{ $ratio }} relative w-full overflow-hidden rounded-box bg-base-200"
    >
        <img
            src="{{ $images[0] }}"
            :src="images[index]"
            alt="{{ $alt }}"
            class="h-full w-full object-cover"
        />

        @if (count($images) > 1)
            <button
                type="button"
                class="btn btn-circle btn-xs absolute left-2 top-1/2 -translate-y-1/2"
                aria-label="Previous image"
                @click="index = (index - 1 + images.length) % images.length"
            >❮</button>

            <button
                type="button"
                class="btn btn-circle btn-xs absolute right-2 top-1/2 -translate-y-1/2"
                aria-label="Next image"
                @click="index = (index + 1) % images.length"
            >❯</button>

            <div class="badge badge-sm badge-neutral absolute bottom-2 left-1/2 -translate-x-1/2">
                <span x-text="index + 1"></span>/<span>{{ count($images) }}</span>
            </div>
        @endif
    </div>
@endif
