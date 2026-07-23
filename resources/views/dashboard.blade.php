<x-layouts::app :title="__('Dashboard')">
    @php($user = auth()->user())
    @php($designs = $user->designs()->latest()->get())
    @php($components = $user->components()->latest()->get())

    <div class="max-w-6xl mx-auto p-4 md:p-8 space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Your studio</h1>
                <p class="text-base-content/60">Manage the designs and components you publish.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('studio.designs.create') }}" class="btn btn-primary btn-sm">New design</a>
                <a href="{{ route('studio.components.create') }}" class="btn btn-outline btn-sm">New component</a>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="stats stats-vertical sm:stats-horizontal shadow w-full bg-base-200">
            <div class="stat">
                <div class="stat-title">Your designs</div>
                <div class="stat-value">{{ $designs->count() }}</div>
                <div class="stat-desc">{{ $designs->where('active', true)->count() }} published</div>
            </div>
            <div class="stat">
                <div class="stat-title">Your components</div>
                <div class="stat-value">{{ $components->count() }}</div>
                <div class="stat-desc">{{ $components->where('active', true)->count() }} published</div>
            </div>
            <div class="stat">
                <div class="stat-title">Collaborations</div>
                <div class="stat-value">{{ $user->collaborations()->count() }}</div>
                <div class="stat-desc">designs you contribute to</div>
            </div>
        </div>

        {{-- Designs --}}
        <section class="space-y-3">
            <h2 class="text-lg font-semibold">Designs</h2>
            @forelse ($designs as $design)
                <div class="flex items-center justify-between gap-4 p-3 rounded-box bg-base-200">
                    <div class="min-w-0">
                        <div class="font-medium truncate">{{ $design->name ?? 'Untitled design' }}</div>
                        <div class="text-sm text-base-content/60">
                            {{ $design->category?->value ?? 'Uncategorised' }}
                            @unless ($design->active) · <span class="badge badge-ghost badge-sm">draft</span> @endunless
                            @if ($design->official) · <span class="badge badge-primary badge-sm">official</span> @endif
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        @if ($design->active)
                            <a href="{{ route('designs.show', $design) }}" class="btn btn-ghost btn-sm">View</a>
                        @endif
                        <a href="{{ route('studio.designs.edit', $design) }}" class="btn btn-outline btn-sm">Edit</a>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60 text-sm">No designs yet. <a href="{{ route('studio.designs.create') }}" class="link">Publish your first.</a></p>
            @endforelse
        </section>

        {{-- Components --}}
        <section class="space-y-3">
            <h2 class="text-lg font-semibold">Components</h2>
            @forelse ($components as $component)
                <div class="flex items-center justify-between gap-4 p-3 rounded-box bg-base-200">
                    <div class="min-w-0">
                        <div class="font-medium truncate">{{ $component->name }}</div>
                        <div class="text-sm text-base-content/60">
                            {{ $component->category?->value ?? 'Uncategorised' }}
                            @unless ($component->active) · <span class="badge badge-ghost badge-sm">draft</span> @endunless
                            @if ($component->official) · <span class="badge badge-primary badge-sm">verified</span> @endif
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        @if ($component->active)
                            <a href="{{ route('components.show', $component) }}" class="btn btn-ghost btn-sm">View</a>
                        @endif
                        <a href="{{ route('studio.components.edit', $component) }}" class="btn btn-outline btn-sm">Edit</a>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60 text-sm">No components yet. <a href="{{ route('studio.components.create') }}" class="link">Add one.</a></p>
            @endforelse
        </section>
    </div>
</x-layouts::app>
