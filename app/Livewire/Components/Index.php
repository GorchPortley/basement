<?php

namespace App\Livewire\Components;

use App\Enums\ComponentCategory;
use App\Models\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component as LivewireComponent;
use Livewire\WithPagination;

class Index extends LivewireComponent
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
        $components = Component::query()
            ->published()
            ->with(['owner', 'media'])
            ->search($this->search)
            ->when($this->category !== '', fn ($q) => $q->where('category', $this->category))
            ->when($this->official, fn ($q) => $q->official())
            ->when($this->sort === 'brand', fn ($q) => $q->orderBy('brand')->orderBy('model'))
            ->when($this->sort === 'newest', fn ($q) => $q->latest())
            ->paginate(16);

        return view('livewire.components.index', [
            'components' => $components,
            'categories' => ComponentCategory::options(),
        ]);
    }
}
