<div class="max-w-6xl mx-auto p-4 md:p-8 space-y-8">
    {{-- Stat tiles --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
        <div class="rounded-box bg-base-200 p-4">
            <div class="text-sm text-base-content/60">Users</div>
            <div class="text-3xl font-bold">{{ $stats['users'] }}</div>
        </div>
        <div class="rounded-box bg-base-200 p-4">
            <div class="text-sm text-base-content/60">Designs</div>
            <div class="text-3xl font-bold">{{ $stats['designs'] }}</div>
            <div class="text-xs text-base-content/50">{{ $stats['designs_published'] }} published</div>
        </div>
        <div class="rounded-box bg-base-200 p-4">
            <div class="text-sm text-base-content/60">Components</div>
            <div class="text-3xl font-bold">{{ $stats['components'] }}</div>
            <div class="text-xs text-base-content/50">{{ $stats['components_published'] }} published</div>
        </div>
        <div class="rounded-box bg-base-200 p-4">
            <div class="text-sm text-base-content/60">Official / verified</div>
            <div class="text-3xl font-bold">{{ $stats['official'] }}</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 border border-base-300 rounded-box p-4">
            <h2 class="font-semibold mb-3">Activity — last 30 days</h2>
            <div wire:ignore x-data="chart(@js($activityConfig))" class="relative h-72">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>
        <div class="border border-base-300 rounded-box p-4">
            <h2 class="font-semibold mb-3">Designs by category</h2>
            @if ($hasCategories)
                <div wire:ignore x-data="chart(@js($categoryConfig))" class="relative h-72">
                    <canvas x-ref="canvas"></canvas>
                </div>
            @else
                <p class="text-sm text-base-content/50">No designs yet.</p>
            @endif
        </div>
    </div>

    <div class="text-sm text-base-content/50 border-t border-base-300 pt-4">
        Brand-account management and licensing analytics can attach here as those features come online.
    </div>
</div>
