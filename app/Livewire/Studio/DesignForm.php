<?php

namespace App\Livewire\Studio;

use App\Enums\DesignAccess;
use App\Enums\DesignCategory;
use App\Models\Design;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class DesignForm extends Component
{
    use WithFileUploads;

    public ?Design $design = null;

    public bool $editing = false;

    public string $name = '';

    public string $summary = '';

    public string $description = '';

    public ?string $category = null;

    public string $access = 'free';

    public $price = null;

    public $build_cost = null;

    public $impedance = null;

    public $power = null;

    public bool $active = false;

    public ?string $forum_slug = null;

    public $image;

    public function mount(?Design $design = null): void
    {
        if ($design && $design->exists) {
            abort_unless($this->owns($design), 403);

            $this->design = $design;
            $this->editing = true;

            $this->fill([
                'name' => $design->name,
                'summary' => (string) $design->summary,
                'description' => (string) $design->description,
                'category' => $design->category?->value,
                'access' => $design->access?->value ?? 'free',
                'price' => $design->price,
                'build_cost' => $design->build_cost,
                'impedance' => $design->impedance,
                'power' => $design->power,
                'active' => $design->active,
                'forum_slug' => $design->forum_slug,
            ]);
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', Rule::in(DesignCategory::values())],
            'access' => ['required', Rule::in(DesignAccess::values())],
            'price' => ['nullable', 'numeric', 'min:0'],
            'build_cost' => ['nullable', 'numeric', 'min:0'],
            'impedance' => ['nullable', 'integer', 'min:1', 'max:32'],
            'power' => ['nullable', 'integer', 'min:0'],
            'active' => ['boolean'],
            'forum_slug' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function save()
    {
        $this->validate();

        $design = $this->design ?? new Design;

        $design->fill([
            'name' => $this->name,
            'summary' => $this->summary ?: null,
            'description' => $this->description ?: null,
            'category' => $this->category,
            'access' => $this->access,
            'price' => $this->price !== null && $this->price !== '' ? (float) $this->price : 0,
            'build_cost' => $this->build_cost !== null && $this->build_cost !== '' ? (float) $this->build_cost : null,
            'impedance' => $this->impedance !== null && $this->impedance !== '' ? (int) $this->impedance : null,
            'power' => $this->power !== null && $this->power !== '' ? (int) $this->power : null,
            'active' => $this->active,
            'forum_slug' => $this->forum_slug ?: null,
        ]);

        if (! $design->exists) {
            $design->owner()->associate(Auth::user());
        }

        $design->save();

        if ($this->image) {
            $design->clearMediaCollection('card');
            $design->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('card');
        }

        session()->flash('status', $this->editing ? 'Design updated.' : 'Design created.');

        return $this->redirectRoute('dashboard', navigate: true);
    }

    protected function owns(Design $design): bool
    {
        return $design->owner_type === User::class && $design->owner_id === Auth::id();
    }

    public function render(): View
    {
        return view('livewire.studio.design-form', [
            'categories' => DesignCategory::options(),
            'accesses' => DesignAccess::options(),
        ]);
    }
}
