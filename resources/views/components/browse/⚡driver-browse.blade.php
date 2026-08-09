<?php

use App\Models\Driver;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $type = '';

    public string $sort = 'newest';

    /**
     * Any change to a filter should put the user back on page one, otherwise
     * they can end up looking at an empty page 4 of a 2 page result.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'type', 'sort']);
        $this->resetPage();
    }

    /**
     * #[Computed] means this only runs once per request even though the view
     * uses it twice (for the grid and for the pagination links).
     */
    #[Computed]
    public function drivers()
    {
        return Driver::query()
            ->with('media')
            ->where('active', true)
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(function ($query) use ($term) {
                    $query->where('payload->meta->brand', 'like', $term)
                        ->orWhere('payload->meta->model', 'like', $term)
                        ->orWhere('payload->meta->tag', 'like', $term);
                });
            })
            ->when($this->type !== '', fn ($query) => $query->where('payload->meta->type', $this->type))
            ->when($this->sort === 'newest', fn ($query) => $query->latest())
            ->when($this->sort === 'oldest', fn ($query) => $query->oldest())
            ->when($this->sort === 'brand', fn ($query) => $query->orderBy('payload->meta->brand'))
            ->when($this->sort === 'price_low', fn ($query) => $query->orderBy('payload->meta->price'))
            ->when($this->sort === 'price_high', fn ($query) => $query->orderByDesc('payload->meta->price'))
            ->paginate(24);
    }

    /**
     * maryUI's x-select wants a list of ['id' => ..., 'name' => ...].
     */
    public function typeOptions(): array
    {
        return collect(Driver::TYPES)
            ->map(fn (string $label, string $value) => ['id' => $value, 'name' => $label])
            ->values()
            ->all();
    }

    public function sortOptions(): array
    {
        return [
            ['id' => 'newest', 'name' => 'Newest first'],
            ['id' => 'oldest', 'name' => 'Oldest first'],
            ['id' => 'brand', 'name' => 'Brand (A–Z)'],
            ['id' => 'price_low', 'name' => 'Price (low to high)'],
            ['id' => 'price_high', 'name' => 'Price (high to low)'],
        ];
    }
};
?>

<div class="flex flex-col items-start gap-4 p-4 lg:flex-row">

    {{-- Search, sort and filter --}}
    <aside class="w-full lg:sticky lg:top-4 lg:w-64 lg:shrink-0">
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4 p-4">
                <x-input
                    label="Search"
                    placeholder="Brand, model or tag"
                    wire:model.live.debounce.400ms="search"
                />

                <x-select
                    label="Type"
                    placeholder="All types"
                    placeholder-value=""
                    :options="$this->typeOptions()"
                    wire:model.live="type"
                />

                <x-select
                    label="Sort by"
                    :options="$this->sortOptions()"
                    wire:model.live="sort"
                />

                <x-button label="Reset" class="btn-ghost btn-sm" wire:click="resetFilters" />
            </div>
        </div>
    </aside>

    {{-- Results --}}
    <div class="flex w-full flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Browse Drivers</h1>
            <span class="text-sm opacity-70">{{ $this->drivers->total() }} drivers</span>
        </div>

        @if ($this->drivers->isEmpty())
            <div class="card border border-base-300 bg-base-100">
                <div class="card-body items-center text-center">
                    <p class="opacity-70">No drivers match those filters.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                @foreach ($this->drivers as $driver)
                    <x-browse.driver-card :driver="$driver" wire:key="driver-{{ $driver->id }}" />
                @endforeach
            </div>

            <x-pagination :rows="$this->drivers" />
        @endif
    </div>
</div>
