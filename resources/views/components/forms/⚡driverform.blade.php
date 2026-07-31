<?php

use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Forms\Components\TextInput;
use Livewire\Component;

new class extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $driver = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema

            ->components([
                TextInput::make('Title'),
            ])
            ->statepath('driver')
            ->model('$this->drivers');
    }

    public function save(): void
    {
        $driver = $this->form->getstate();

        Driver::create($driver);
    }

    public function resetForm(): void
    {
        $this->driver = [];
        $this->form->fill();
    }
};
?>

<div>
    <x-card title="Manage Your Drivers" class="bg-neutral-content rounded-none p-15">
    <div>
    {{ $this->form }}
    </div>
    </x-card>
</div>
