<?php

use App\Models\Design;
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
    public function designs()
    {
        return Design::query()
            ->with(['media', 'owner'])
            ->where('active', true)
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(function ($query) use ($term) {
                    $query->where('payload->meta->title', 'like', $term)
                        ->orWhere('payload->meta->tagline', 'like', $term);
                });
            })
            ->when($this->type !== '', fn ($query) => $query->where('payload->meta->type', $this->type))
            ->when($this->sort === 'newest', fn ($query) => $query->latest())
            ->when($this->sort === 'oldest', fn ($query) => $query->oldest())
            ->when($this->sort === 'title', fn ($query) => $query->orderBy('payload->meta->title'))
            ->when($this->sort === 'cost_low', fn ($query) => $query->orderBy('payload->meta->build_cost'))
            ->when($this->sort === 'cost_high', fn ($query) => $query->orderByDesc('payload->meta->build_cost'))
            ->paginate(24);
    }

    /**
     * maryUI's x-select wants a list of ['id' => ..., 'name' => ...].
     */
    public function typeOptions(): array
    {
        return collect(Design::TYPES)
            ->map(fn (string $label, string $value) => ['id' => $value, 'name' => $label])
            ->values()
            ->all();
    }

    public function sortOptions(): array
    {
        return [
            ['id' => 'newest', 'name' => 'Newest first'],
            ['id' => 'oldest', 'name' => 'Oldest first'],
            ['id' => 'title', 'name' => 'Title (A–Z)'],
            ['id' => 'cost_low', 'name' => 'Build cost (low to high)'],
            ['id' => 'cost_high', 'name' => 'Build cost (high to low)'],
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
                    placeholder="Title or tag line"
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
            <h1 class="text-xl font-semibold">Browse Designs</h1>
            <span class="text-sm opacity-70">{{ $this->designs->total() }} designs</span>
        </div>

        @if ($this->designs->isEmpty())
            <div class="card border border-base-300 bg-base-100">
                <div class="card-body items-center text-center">
                    <p class="opacity-70">No designs match those filters.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($this->designs as $design)
                    <x-browse.design-card :design="$design" wire:key="design-{{ $design->id }}" />
                @endforeach
            </div>

            <x-pagination :rows="$this->designs" />
        @endif
    </div>
</div>
