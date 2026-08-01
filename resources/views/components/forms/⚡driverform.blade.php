<?php

use App\Models\Driver;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
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
                Section::make('Driver Information')
                    ->description('Basic Driver Information')
                    ->schema([
                        TextInput::make('payload.meta.type'),
                        TextInput::make('payload.meta.brand')
                            ->datalist(fn () => Driver::getBrands()),
                        TextInput::make('payload.meta.model'),
                        TextInput::make('payload.meta.tag'),
                        TextInput::make('payload.meta.link'),
                        TextInput::make('payload.meta.price')->numeric(),
                        SpatieMediaLibraryFileUpload::make('prod_image')
                            ->multiple()
                            ->disk('uploads')
                            ->directory('driver_files')
                            ->visibility('public')
                            ->collection('prod_img')
                            ->conversionsDisk('temp')
                            ->responsiveImages(),
                    ]),
                Section::make('Descriptions')
                    ->description('Describe the component')
                    ->schema([
                        RichEditor::make('payload.descriptions.description'),
                        SpatieMediaLibraryFileUpload::make('description_img')
                            ->multiple()
                            ->disk('uploads')
                            ->directory('driver_files')
                            ->visibility('public')
                            ->collection('description_img')
                            ->conversionsDisk('uploads')
                            ->responsiveImages(),
                    ]),
                Section::make('Specifications')
                    ->description('Specifications of the Driver')
                    ->schema([
                        TextInput::make('payload.specs.size'),
                        TextInput::make('payload.specs.type'),
                        TextInput::make('payload.specs.tsparam'),
                    ]),
            ])
            ->statePath('driver')
            ->model(Driver::class);
    }

    public function save(): void
    {
        $driver = Driver::create($this->form->getState());
        $this->form->record($driver)->saveRelationships();
        $this->resetform();
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
    <div class="flex bg-neutral-content justify-center">
    <x-card title="Manage Your Drivers" class="rounded-none w-300">
        <div>
            {{ $this->form }}
            <x-button label="Save design" class="btn-primary" wire:click="save" spinner="save" />
        </div>
    </x-card>
</div>
</div>
