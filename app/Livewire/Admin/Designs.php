<?php

namespace App\Livewire\Admin;

use App\Models\Design;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component as LivewireComponent;
use Livewire\WithPagination;

class Designs extends LivewireComponent
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    /** Runs on every request (initial + actions) — guards all entry points. */
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
        $design = Design::findOrFail($id);
        $design->update(['official' => ! $design->official]);
    }

    public function toggleActive(int $id): void
    {
        $design = Design::findOrFail($id);
        $design->update(['active' => ! $design->active]);
    }

    public function delete(int $id): void
    {
        Design::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $designs = Design::query()
            ->with('owner')
            ->search($this->search)
            ->latest()
            ->paginate(15);

        return view('livewire.admin.designs', ['designs' => $designs]);
    }
}
