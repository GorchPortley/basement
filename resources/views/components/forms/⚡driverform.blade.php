<?php

use App\Models\Driver;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Livewire\Component;

new class extends Component implements HasSchemas {
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
                Section::make('Heading')
                    ->description('Meta Fields')
                    ->schema([
                        TextInput::make('payload.meta.brand'),
                        TextInput::make('payload.meta.model'),
                        TextInput::make('payload.meta.tag'),
                        TextInput::make('payload.meta.link'),
                        TextInput::make('payload.meta.price')->numeric(),
                        SpatieMediaLibraryFileUpload::make('images')
                            ->disk('uploads')
                            ->directory('driver_files')
                            ->visibility('public')
                            ->collection('images')
                            ->conversionsDisk('assets')
                            ->responsiveImages(),
                    ]),
            ])
            ->statePath('driver')
            ->model(Driver::class);
    }

    public function save(): void
    {
        $driver = Driver::create($this->form->getState());
        $this->form->record($driver)->saveRelationships();
        $this->form->fill();
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
            <x-button label="Save design" class="btn-primary" wire:click="save" spinner="save" />
        </div>
    </x-card>
</div>
