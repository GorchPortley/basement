<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="invisible md:visible sticky max-w-full max-h-24 bg-secondary-content">
        <div class="flex flex-row">
        <x-mary-button label="Drivers" link="{{ route('dashboarddrivers') }}" :class="'rounded-none basis-1/3 hover:bg-secondary ' . (request()->routeIs('dashboarddrivers') ? 'bg-secondary' : 'bg-secondary-content')" />
        <x-mary-button label="Designs" link="{{ route('dashboarddesigns') }}" :class="'rounded-none basis-1/3 hover:bg-secondary ' . (request()->routeIs('dashboarddesigns') ? 'bg-secondary' : 'bg-secondary-content')" />
        <x-mary-button label="Settings" class="'rounded-none basis-1/3 bg-secondary-content hover:bg-secondary" />
    </div>
</div>
</div>
