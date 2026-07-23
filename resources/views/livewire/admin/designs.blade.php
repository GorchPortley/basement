<div class="max-w-6xl mx-auto p-4 md:p-8">
    <div class="flex items-center justify-between gap-3 mb-4">
        <h1 class="text-xl font-bold">Designs</h1>
        <label class="input input-bordered input-sm flex items-center gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search…" />
        </label>
    </div>

    <div class="overflow-x-auto border border-base-300 rounded-box">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Owner</th>
                    <th>Category</th>
                    <th class="text-center">Official</th>
                    <th class="text-center">Published</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($designs as $design)
                    <tr wire:key="admin-design-{{ $design->id }}">
                        <td class="font-medium">
                            @if ($design->active)
                                <a href="{{ route('designs.show', $design) }}" class="link link-hover">{{ $design->name ?? 'Untitled' }}</a>
                            @else
                                {{ $design->name ?? 'Untitled' }}
                            @endif
                        </td>
                        <td class="text-sm">{{ $design->owner?->name ?? '—' }}</td>
                        <td class="text-sm">{{ $design->category?->value ?? '—' }}</td>
                        <td class="text-center">
                            <input type="checkbox" class="toggle toggle-sm toggle-primary"
                                   @checked($design->official)
                                   wire:click="toggleOfficial({{ $design->id }})" />
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="toggle toggle-sm"
                                   @checked($design->active)
                                   wire:click="toggleActive({{ $design->id }})" />
                        </td>
                        <td class="text-right">
                            <button class="btn btn-ghost btn-xs text-error"
                                    wire:click="delete({{ $design->id }})"
                                    wire:confirm="Delete this design permanently?">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-base-content/50 py-8">No designs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $designs->links() }}</div>
</div>
