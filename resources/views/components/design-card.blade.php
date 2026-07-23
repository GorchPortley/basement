@props(['design'])

@php($image = $design->getFirstMediaUrl('card'))

<a href="{{ route('designs.show', $design) }}"
   class="card bg-base-100 border border-base-300 hover:border-primary transition group">
    <figure class="aspect-video bg-base-200 overflow-hidden">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $design->name }}"
                 class="object-cover w-full h-full group-hover:scale-105 transition" />
        @else
            <div class="w-full h-full flex items-center justify-center text-base-content/30 text-sm">No image</div>
        @endif
    </figure>
    <div class="card-body p-4 gap-2">
        <div class="flex items-start justify-between gap-2">
            <h3 class="font-semibold leading-tight">{{ $design->name }}</h3>
            @if ($design->official)
                <span class="badge badge-primary badge-sm shrink-0">official</span>
            @endif
        </div>
        @if ($design->summary)
            <p class="text-sm text-base-content/60 line-clamp-2">{{ $design->summary }}</p>
        @endif
        <div class="flex items-center justify-between mt-1">
            <span class="badge badge-ghost">{{ $design->category?->value ?? '—' }}</span>
            <span class="text-sm font-medium">
                {{ $design->access?->isOpen() ? 'Free' : '$'.number_format((float) $design->price, 2) }}
            </span>
        </div>
        <div class="text-xs text-base-content/50">by {{ $design->owner?->name ?? 'Unknown' }}</div>
    </div>
</a>
