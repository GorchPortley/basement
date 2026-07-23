<x-layouts::app :title="$component->name">
    @php($image = $component->getFirstMediaUrl('card'))
    @php($specs = $component->factorySpecs())

    <div class="max-w-6xl mx-auto p-4 md:p-8 space-y-8">

        {{-- Header --}}
        <div class="grid gap-6 md:grid-cols-[2fr_3fr]">
            <div class="aspect-square bg-base-200 rounded-box overflow-hidden border border-base-300">
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $component->name }}" class="object-cover w-full h-full" />
                @else
                    <div class="w-full h-full flex items-center justify-center text-base-content/30">No image</div>
                @endif
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="badge badge-ghost">{{ $component->category?->value ?? '—' }}</span>
                    @if ($component->official)<span class="badge badge-primary">verified</span>@endif
                </div>
                <div class="text-sm uppercase tracking-wide text-base-content/50">{{ $component->brand }}</div>
                <h1 class="text-3xl font-bold">{{ $component->model }}</h1>
                @if ($component->summary)<p class="text-base-content/70">{{ $component->summary }}</p>@endif

                <div class="flex flex-wrap gap-2 pt-2">
                    @if ($component->size)<div class="badge badge-lg">{{ rtrim(rtrim((string) $component->size, '0'), '.') }}"</div>@endif
                    @if ($component->impedance)<div class="badge badge-lg">{{ $component->impedance }}Ω</div>@endif
                    @if ($component->power)<div class="badge badge-lg">{{ $component->power }}W</div>@endif
                    @if ($component->price)<div class="badge badge-lg">${{ number_format((float) $component->price, 2) }}</div>@endif
                </div>

                @if ($component->link)
                    <a href="{{ $component->link }}" target="_blank" rel="nofollow noopener"
                       class="btn btn-primary btn-sm mt-2">Where to buy ↗</a>
                @endif
            </div>
        </div>

        {{-- Factory response --}}
        <section>
            <h2 class="text-lg font-semibold mb-3">Factory response</h2>
            <livewire:frequency-response-viewer :component="$component" />
        </section>

        {{-- Factory / Thiele-Small parameters (from payload) --}}
        @if (! empty($specs))
            <section>
                <h2 class="text-lg font-semibold mb-3">Specifications</h2>
                <div class="overflow-x-auto border border-base-300 rounded-box">
                    <table class="table table-sm">
                        <tbody>
                            @foreach ($specs as $key => $value)
                                <tr>
                                    <td class="font-medium w-1/3">{{ is_string($key) ? $key : ($value['label'] ?? '') }}</td>
                                    <td>{{ is_scalar($value) ? $value : ($value['value'] ?? '') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- Description --}}
        @if ($component->description)
            <section>
                <h2 class="text-lg font-semibold mb-3">Details</h2>
                <div class="prose max-w-none">{!! nl2br(e($component->description)) !!}</div>
            </section>
        @endif

        {{-- Designs using this component --}}
        <section>
            <h2 class="text-lg font-semibold mb-3">Used in designs</h2>
            @if ($component->designs->isEmpty())
                <p class="text-base-content/50 text-sm">Not used in any published design yet.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($component->designs as $design)
                        <div wire:key="d-{{ $design->id }}">
                            <x-design-card :design="$design" />
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
