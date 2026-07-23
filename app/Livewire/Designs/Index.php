<?php

namespace App\Livewire\Designs;

use App\Enums\DesignCategory;
use App\Models\Design;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $category = '';

    #[Url]
    public bool $official = false;

    #[Url]
    public string $sort = 'newest';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category', 'official', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'category', 'official', 'sort']);
        $this->resetPage();
    }

    public function render(): View
    {
        $designs = Design::query()
            ->published()
            ->with(['owner', 'media'])
            ->search($this->search)
            ->when($this->category !== '', fn ($q) => $q->where('category', $this->category))
            ->when($this->official, fn ($q) => $q->official())
            ->when($this->sort === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($this->sort === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($this->sort === 'newest', fn ($q) => $q->latest())
            ->paginate(12);

        return view('livewire.designs.index', [
            'designs' => $designs,
            'categories' => DesignCategory::options(),
        ]);
    }
}
