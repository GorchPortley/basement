<?php

namespace App\Livewire\Studio;

use App\Enums\ComponentCategory;
use App\Models\Component;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component as LivewireComponent;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ComponentForm extends LivewireComponent
{
    use WithFileUploads;

    public ?Component $component = null;

    public bool $editing = false;

    public string $brand = '';

    public string $model = '';

    public ?string $category = null;

    public $size = null;

    public ?string $impedance = null;

    public $power = null;

    public $price = null;

    public ?string $link = null;

    public string $summary = '';

    public string $description = '';

    public bool $active = false;

    public ?string $forum_slug = null;

    public $image;

    public $frd; // factory frequency response (.frd / .txt)

    public function mount(?Component $component = null): void
    {
        if ($component && $component->exists) {
            abort_unless($this->owns($component), 403);

            $this->component = $component;
            $this->editing = true;

            $this->fill([
                'brand' => $component->brand,
                'model' => $component->model,
                'category' => $component->category?->value,
                'size' => $component->size,
                'impedance' => $component->impedance,
                'power' => $component->power,
                'price' => $component->price,
                'link' => $component->link,
                'summary' => (string) $component->summary,
                'description' => (string) $component->description,
                'active' => $component->active,
                'forum_slug' => $component->forum_slug,
            ]);
        }
    }

    protected function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'category' => ['nullable', Rule::in(ComponentCategory::values())],
            'size' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'impedance' => ['nullable', 'string', 'max:32'],
            'power' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'link' => ['nullable', 'url', 'max:2048'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'active' => ['boolean'],
            'forum_slug' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'frd' => ['nullable', 'file', 'max:2048'],
        ];
    }

    public function save()
    {
        $this->validate();

        $component = $this->component ?? new Component;

        $component->fill([
            'brand' => $this->brand,
            'model' => $this->model,
            'category' => $this->category,
            'size' => $this->size !== null && $this->size !== '' ? (float) $this->size : null,
            'impedance' => $this->impedance ?: null,
            'power' => $this->power !== null && $this->power !== '' ? (int) $this->power : null,
            'price' => $this->price !== null && $this->price !== '' ? (float) $this->price : null,
            'link' => $this->link ?: null,
            'summary' => $this->summary ?: null,
            'description' => $this->description ?: null,
            'active' => $this->active,
            'forum_slug' => $this->forum_slug ?: null,
        ]);

        if (! $component->exists) {
            $component->owner()->associate(Auth::user());
        }

        $component->save();

        if ($this->image) {
            $component->clearMediaCollection('card');
            $component->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('card');
        }

        if ($this->frd) {
            $component->addMedia($this->frd->getRealPath())
                ->usingFileName($this->frd->getClientOriginalName())
                ->toMediaCollection('frequency');
        }

        session()->flash('status', $this->editing ? 'Component updated.' : 'Component created.');

        return $this->redirectRoute('dashboard', navigate: true);
    }

    protected function owns(Component $component): bool
    {
        return $component->owner_type === User::class && $component->owner_id === Auth::id();
    }

    public function render(): View
    {
        return view('livewire.studio.component-form', [
            'categories' => ComponentCategory::options(),
        ]);
    }
}
