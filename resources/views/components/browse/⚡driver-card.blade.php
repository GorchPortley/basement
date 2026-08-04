<?php

use Livewire\Component;

new class extends Component
{
    public $driver;

    public function mount($driver = Driver::class)
    {
        $this->driver = $driver;
    }
};
?>

<div>
    <div class="max-w-full flex flex-col border items-center justify-center rounded-sm p-2 m-5 bg-secondary text-secondary-content">
        <img src="https://placehold.co/300x300" />
        <text class="pt-2">{{ $driver->payload['meta']['brand'] ?? 'No Brand' }} {{ $driver->payload['meta']['model'] ?? 'No Model' }} </text>
        <text class="pt-2">{{ $driver->payload['meta']['size'] ?? 'No Size' }} inch {{ $driver->payload['meta']['type'] ?? 'No Type' }}</text>
        <text class="pt-2">{{ $driver->payload['meta']['impedance'] ?? 'No Znom' }} Ohm - {{ $driver->payload['specs']['tsparam']['Pe'] ?? 'Unknown' }}</text>
        <div class="w-full pt-2 flex flex-row items-center justify-center ">
            <x-mary-button label="View Driver" class="mr-1" />
            <x-mary-button label="Save Driver" class="ml-1" />
        </div>
    </div>
</div>
