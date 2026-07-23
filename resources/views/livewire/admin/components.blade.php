<div class="max-w-6xl mx-auto p-4 md:p-8">
    <div class="flex items-center justify-between gap-3 mb-4">
        <h1 class="text-xl font-bold">Components</h1>
        <label class="input input-bordered input-sm flex items-center gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search…" />
        </label>
    </div>

    <div class="overflow-x-auto border border-base-300 rounded-box">
        <table class="table">
            <thead>
                <tr>
                    <th>Component</th>
                    <th>Owner</th>
                    <th>Type</th>
                    <th class="text-center">Verified</th>
                    <th class="text-center">Published</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($components as $component)
                    <tr wire:key="admin-component-{{ $component->id }}">
                        <td class="font-medium">
                            @if ($component->active)
                                <a href="{{ route('components.show', $component) }}" class="link link-hover">{{ $component->name }}</a>
                            @else
                                {{ $component->name }}
                            @endif
                        </td>
                        <td class="text-sm">{{ $component->owner?->name ?? '—' }}</td>
                        <td class="text-sm">{{ $component->category?->value ?? '—' }}</td>
                        <td class="text-center">
                            <input type="checkbox" class="toggle toggle-sm toggle-primary"
                                   @checked($component->official)
                                   wire:click="toggleOfficial({{ $component->id }})" />
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="toggle toggle-sm"
                                   @checked($component->active)
                                   wire:click="toggleActive({{ $component->id }})" />
                        </td>
                        <td class="text-right">
                            <button class="btn btn-ghost btn-xs text-error"
                                    wire:click="delete({{ $component->id }})"
                                    wire:confirm="Delete this component permanently?">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-base-content/50 py-8">No components found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $components->links() }}</div>
</div>
