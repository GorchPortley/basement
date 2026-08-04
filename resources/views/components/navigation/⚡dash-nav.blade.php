<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="invisible md:visible sticky max-w-full max-h-24 bg-secondary text-secondary-content">
        <div class="flex flex-row">
        <x-mary-button label="Drivers" link="{{ route('dashboarddrivers') }}" :class="'rounded-none basis-1/3 hover:text-accent-content hover:bg-accent ' . (request()->routeIs('dashboarddrivers') ? 'bg-accent text-accent-content' : '' )" />
        <x-mary-button label="Designs" link="{{ route('dashboarddesigns') }}" :class="'rounded-none basis-1/3 hover:text-accent-content hover:bg-accent ' . (request()->routeIs('dashboarddesigns') ? 'bg-accent text-accent-content' : '' )" />
        <x-mary-button label="Settings" class="rounded-none basis-1/3 hover:bg-accent hover:text-accent-content" />
    </div>
</div>
</div>
