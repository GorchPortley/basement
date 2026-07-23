<x-layouts::app :title="__('Welcome')">
    <div class="hero bg-base-200 border-b border-base-300">
        <div class="hero-content text-center py-16">
            <div class="max-w-2xl">
                <h1 class="text-4xl font-bold">The open speaker design library</h1>
                <p class="py-4 text-base-content/70">
                    Publish, browse, and study DIY loudspeaker designs and the drivers behind them —
                    complete with measured frequency response. Free and equal for every builder.
                </p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('designs.index') }}" class="btn btn-primary">Browse designs</a>
                    <a href="{{ route('components.index') }}" class="btn btn-outline">Browse components</a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto p-4 md:p-8 grid gap-6 sm:grid-cols-3">
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-base">Designs</h2>
                <p class="text-sm text-base-content/60">Full speaker projects with BOMs, enclosure and crossover files.</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-base">Components</h2>
                <p class="text-sm text-base-content/60">A verifiable driver database with factory FRD/ZMA data.</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-base">Response viewer</h2>
                <p class="text-sm text-base-content/60">See how multiple drivers sum acoustically, right in the browser.</p>
            </div>
        </div>
    </div>
</x-layouts::app>
