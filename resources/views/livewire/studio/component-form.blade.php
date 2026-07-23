<div class="max-w-3xl mx-auto p-4 md:p-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $editing ? 'Edit component' : 'New component' }}</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Cancel</a>
    </div>

    <x-form wire:submit="save" class="space-y-5">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input label="Brand" wire:model="brand" placeholder="e.g. Dayton Audio" required />
            <x-input label="Model" wire:model="model" placeholder="e.g. DC28F-8" required />
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-select label="Type" wire:model="category" :options="$categories" placeholder="Select" />
            <x-input label="Size (in)" wire:model="size" type="number" step="0.5" min="0" />
            <x-input label="Impedance (Ω)" wire:model="impedance" placeholder="e.g. 8" />
            <x-input label="Power (W)" wire:model="power" type="number" min="0" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-input label="Price ($)" wire:model="price" type="number" step="0.01" min="0" />
            <x-input label="Where to buy (URL)" wire:model="link" placeholder="https://…" />
        </div>

        <x-textarea label="Summary" wire:model="summary" rows="2" />
        <x-textarea label="Details" wire:model="description" rows="5" />

        <x-input label="Forum discussion slug" wire:model="forum_slug" />

        <div class="grid gap-4 sm:grid-cols-2">
            <x-file label="Card image" wire:model="image" accept="image/*" hint="PNG/JPG, up to 5 MB." />
            <x-file label="Factory response (.frd)" wire:model="frd"
                    hint="Frequency / phase text file — powers the response viewer." />
        </div>

        <x-checkbox label="Publish (list publicly)" wire:model="active" />

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a>
            <x-button label="{{ $editing ? 'Save changes' : 'Create component' }}" type="submit"
                      class="btn-primary" spinner="save" />
        </div>
    </x-form>
</div>
