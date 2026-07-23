@props(['component'])

@php($image = $component->getFirstMediaUrl('card'))

<a href="{{ route('components.show', $component) }}"
   class="card bg-base-100 border border-base-300 hover:border-primary transition group">
    <figure class="aspect-square bg-base-200 overflow-hidden">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $component->name }}"
                 class="object-cover w-full h-full group-hover:scale-105 transition" />
        @else
            <div class="w-full h-full flex items-center justify-center text-base-content/30 text-sm">No image</div>
        @endif
    </figure>
    <div class="card-body p-4 gap-2">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <div class="text-xs uppercase tracking-wide text-base-content/50">{{ $component->brand }}</div>
                <h3 class="font-semibold leading-tight truncate">{{ $component->model }}</h3>
            </div>
            @if ($component->official)
                <span class="badge badge-primary badge-sm shrink-0" title="Manufacturer verified">verified</span>
            @endif
        </div>
        <div class="flex items-center justify-between mt-1">
            <span class="badge badge-ghost">{{ $component->category?->value ?? '—' }}</span>
            <span class="text-sm text-base-content/70">
                @if ($component->size){{ rtrim(rtrim((string) $component->size, '0'), '.') }}"@endif
                @if ($component->impedance) · {{ $component->impedance }}Ω @endif
            </span>
        </div>
    </div>
</a>
