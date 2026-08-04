@php
    $drivers = \App\Models\Driver::where('active', 1)->get();
@endphp

<x-layouts::app :title="__('Browse Drivers')">
    <div class="flex items-start gap-4">
        <div class="w-1/6 border sticky top-18">
            SearchSortFilter BOX
        </div>
        <div class="flex-1 flex flex-col gap-2">
            <div class="h-60 border">
                Top Banner
            </div>
            <div class="grid grid-cols-5 gap-1">
                @foreach ($drivers as $driver)
                    <livewire:browse.driver-card :driver="$driver" />
                @endforeach
            </div>
        </div>
    </div>
</x-layouts::app>
