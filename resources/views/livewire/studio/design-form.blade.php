<div class="max-w-3xl mx-auto p-4 md:p-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $editing ? 'Edit design' : 'New design' }}</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Cancel</a>
    </div>

    <x-form wire:submit="save" class="space-y-5">
        <x-input label="Name" wire:model="name" placeholder="e.g. Overnight Sensations MT" required />

        <x-textarea label="Summary" wire:model="summary" rows="2"
                    hint="One line shown on cards and search results." />

        <div class="grid gap-4 sm:grid-cols-2">
            <x-select label="Category" wire:model="category" :options="$categories" placeholder="Select a type" />
            <x-select label="Access" wire:model="access" :options="$accesses" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-input label="Price ($)" wire:model="price" type="number" step="0.01" min="0" />
            <x-input label="Build cost ($)" wire:model="build_cost" type="number" step="0.01" min="0" />
            <x-input label="Impedance (Ω)" wire:model="impedance" type="number" min="1" max="32" />
            <x-input label="Power (W)" wire:model="power" type="number" min="0" />
        </div>

        <x-textarea label="Description" wire:model="description" rows="6" />

        <x-input label="Forum discussion slug" wire:model="forum_slug"
                 hint="Optional — links to a thread on the forum." />

        <x-file label="Card image" wire:model="image" accept="image/*" hint="PNG/JPG, up to 5 MB." />

        <x-checkbox label="Publish (list publicly)" wire:model="active" />

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a>
            <x-button label="{{ $editing ? 'Save changes' : 'Create design' }}" type="submit"
                      class="btn-primary" spinner="save" />
        </div>
    </x-form>
</div>
