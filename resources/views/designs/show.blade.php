<x-layouts::app :title="$design->name">
    @php($image = $design->getFirstMediaUrl('card'))
    @php($isOwner = auth()->id() === $design->owner_id && $design->owner_type === \App\Models\User::class)
    @php($canDownload = $design->access?->isOpen() || $isOwner)

    <div class="max-w-6xl mx-auto p-4 md:p-8 space-y-8">

        {{-- Header --}}
        <div class="grid gap-6 md:grid-cols-[2fr_3fr]">
            <div class="aspect-video bg-base-200 rounded-box overflow-hidden border border-base-300">
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $design->name }}" class="object-cover w-full h-full" />
                @else
                    <div class="w-full h-full flex items-center justify-center text-base-content/30">No image</div>
                @endif
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="badge badge-ghost">{{ $design->category?->value ?? '—' }}</span>
                    @if ($design->official)<span class="badge badge-primary">official</span>@endif
                    @unless ($design->active)<span class="badge badge-outline">draft preview</span>@endunless
                </div>
                <h1 class="text-3xl font-bold">{{ $design->name }}</h1>
                <p class="text-base-content/70">{{ $design->summary }}</p>

                <div class="text-sm text-base-content/60">
                    by <span class="font-medium text-base-content">{{ $design->owner?->name ?? 'Unknown' }}</span>
                    @if ($design->collaborators->isNotEmpty())
                        with {{ $design->collaborators->pluck('name')->join(', ') }}
                    @endif
                </div>

                {{-- Spec chips --}}
                <div class="flex flex-wrap gap-2 pt-2">
                    @if ($design->impedance)<div class="badge badge-lg">{{ $design->impedance }}Ω</div>@endif
                    @if ($design->power)<div class="badge badge-lg">{{ $design->power }}W</div>@endif
                    @if ($design->build_cost)<div class="badge badge-lg">~${{ number_format((float) $design->build_cost, 0) }} build</div>@endif
                    <div class="badge badge-lg badge-primary">{{ $design->access?->label() ?? 'Free' }}</div>
                </div>

                @if ($design->forum_slug && config('services.forum.url'))
                    <a href="{{ rtrim(config('services.forum.url'), '/') }}/d/{{ $design->forum_slug }}"
                       class="btn btn-outline btn-sm mt-2">Discuss on the forum</a>
                @endif
            </div>
        </div>

        {{-- Frequency response viewer --}}
        <section>
            <h2 class="text-lg font-semibold mb-3">Frequency response</h2>
            <livewire:frequency-response-viewer :design="$design" />
        </section>

        {{-- The recipe: components used --}}
        <section>
            <h2 class="text-lg font-semibold mb-3">Components</h2>
            @if ($design->components->isEmpty())
                <p class="text-base-content/50 text-sm">No components listed for this design.</p>
            @else
                <div class="overflow-x-auto border border-base-300 rounded-box">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Component</th>
                                <th>Qty</th>
                                <th>Crossover</th>
                                <th>Enclosure</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($design->components as $component)
                                @php($p = $component->pivot)
                                <tr>
                                    <td><span class="badge badge-ghost">{{ $p->position?->value ?? '—' }}</span></td>
                                    <td>
                                        <a href="{{ route('components.show', $component) }}" class="link link-hover font-medium">
                                            {{ $component->name }}
                                        </a>
                                    </td>
                                    <td>{{ $p->quantity }}</td>
                                    <td class="text-sm text-base-content/70">
                                        @if ($p->low_frequency || $p->high_frequency)
                                            {{ $p->low_frequency ? rtrim(rtrim((string) $p->low_frequency, '0'), '.').'Hz' : '' }}
                                            –
                                            {{ $p->high_frequency ? rtrim(rtrim((string) $p->high_frequency, '0'), '.').'Hz' : '' }}
                                        @else — @endif
                                    </td>
                                    <td class="text-sm text-base-content/70">
                                        {{ $p->air_volume ? rtrim(rtrim((string) $p->air_volume, '0'), '.').' L' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Description --}}
        @if ($design->description)
            <section>
                <h2 class="text-lg font-semibold mb-3">About this design</h2>
                <div class="prose max-w-none">{!! nl2br(e($design->description)) !!}</div>
            </section>
        @endif

        {{-- Bill of materials (from payload) --}}
        @php($bom = $design->billOfMaterials())
        @if (! empty($bom))
            <section>
                <h2 class="text-lg font-semibold mb-3">Bill of materials</h2>
                <div class="overflow-x-auto border border-base-300 rounded-box">
                    <table class="table table-sm">
                        <thead><tr><th>Item</th><th>Qty</th><th>Notes</th></tr></thead>
                        <tbody>
                            @foreach ($bom as $row)
                                <tr>
                                    <td>{{ $row['item'] ?? $row['name'] ?? '—' }}</td>
                                    <td>{{ $row['quantity'] ?? $row['qty'] ?? '' }}</td>
                                    <td class="text-base-content/60">{{ $row['notes'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- Files / downloads (gated) --}}
        <section>
            <h2 class="text-lg font-semibold mb-3">Files</h2>
            @if ($canDownload)
                @php($collections = ['frd' => 'Frequency data', 'enclosure' => 'Enclosure', 'electronic' => 'Electronics', 'other' => 'Other'])
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($collections as $key => $label)
                        @php($files = $design->getMedia($key))
                        @if ($files->isNotEmpty())
                            <div class="border border-base-300 rounded-box p-4">
                                <div class="font-medium mb-2">{{ $label }}</div>
                                <ul class="space-y-1">
                                    @foreach ($files as $file)
                                        <li>
                                            <a href="{{ $file->getUrl() }}" class="link link-primary text-sm" download>
                                                {{ $file->file_name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="alert">
                    <span>This design is gated by its author. Purchasing to unlock files is coming soon.</span>
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
