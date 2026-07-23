<?php

namespace App\Livewire\Admin;

use App\Models\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component as LivewireComponent;
use Livewire\WithPagination;

class Components extends LivewireComponent
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function boot(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleOfficial(int $id): void
    {
        $component = Component::findOrFail($id);
        $component->update(['official' => ! $component->official]);
    }

    public function toggleActive(int $id): void
    {
        $component = Component::findOrFail($id);
        $component->update(['active' => ! $component->active]);
    }

    public function delete(int $id): void
    {
        Component::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $components = Component::query()
            ->with('owner')
            ->search($this->search)
            ->latest()
            ->paginate(15);

        return view('livewire.admin.components', ['components' => $components]);
    }
}
