<?php

use App\Models\Driver;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

new class extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;
    use RestrictsFileUploadsToSchemaComponents;

    /**
     * The list of drivers this user owns. Creating, editing and deleting all
     * happen through the table's actions, which reuse driverForm() below.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(Driver::query()->where('owner_id', auth()->id()))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('payload.meta.brand')
                    ->label('Brand'),
                TextColumn::make('payload.meta.model')
                    ->label('Model'),
                TextColumn::make('payload.meta.type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => Driver::TYPES[$state] ?? 'Unspecified')
                    ->badge(),
                IconColumn::make('active')
                    ->label('Published')
                    ->boolean(),
            ])
            // A table with no filters at all renders an empty filter form,
            // which Filament's partial renderer chokes on, so keep one here.
            ->filters([
                TernaryFilter::make('active')
                    ->label('Published'),
            ])
            ->recordActions([
                EditAction::make('editDriver')
                    ->schema(fn (Schema $schema): Schema => $this->driverForm($schema)),
                DeleteAction::make('deleteDriver'),
            ])
            ->toolbarActions([
                CreateAction::make('newDriver')
                    ->label('Add a driver')
                    ->schema(fn (Schema $schema): Schema => $this->driverForm($schema)),
            ])
            ->emptyStateHeading('No drivers yet')
            ->emptyStateDescription('Add a driver to start building your library.');
    }

    /**
     * The create/edit form. It is handed the schema owned by whichever action
     * opened it, so it must not set its own state path.
     */
    public function driverForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Driver Information')
                    ->description('Basic Driver Information')
                    ->schema([
                        Toggle::make('active')
                            ->label('Published')
                            ->helperText('Published drivers show up in the public browse page.')
                            ->default(false),
                        Select::make('payload.meta.type')
                            ->label('Type')
                            ->options(Driver::TYPES)
                            ->required(),
                        TextInput::make('payload.meta.brand')
                            ->label('Brand')
                            ->required()
                            ->datalist(fn () => Driver::getBrands()),
                        TextInput::make('payload.meta.model')
                            ->label('Model')
                            ->required(),
                        TextInput::make('payload.meta.impedance')
                            ->label('Nominal impedance')
                            ->numeric()
                            ->suffix('Ω'),
                        TextInput::make('payload.meta.size')
                            ->label('Size')
                            ->numeric()
                            ->suffix('in'),
                        TextInput::make('payload.meta.tag')
                            ->label('Tag line')
                            ->maxLength(120),
                        TextInput::make('payload.meta.link')
                            ->label('Product page')
                            ->url()
                            ->placeholder('https://'),
                        TextInput::make('payload.meta.price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$'),
                        SpatieMediaLibraryFileUpload::make('prod_image')
                            ->label('Product images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('prod_img')
                            ->conversionsDisk('temp')
                            ->responsiveImages(),
                    ]),
                Section::make('Descriptions')
                    ->description('Describe the driver')
                    ->schema([
                        RichEditor::make('payload.descriptions.description')
                            ->label('Description'),
                    ]),
                Section::make('Specifications')
                    ->description('Specifications of the Driver')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('datasheet')
                            ->label('Datasheets')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('datasheet'),
                        SpatieMediaLibraryFileUpload::make('zma')
                            ->label('Impedance Files')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('zma'),
                        SpatieMediaLibraryFileUpload::make('frd')
                            ->label('Frequency Response Files')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('frd'),
                        SpatieMediaLibraryFileUpload::make('other')
                            ->label('Other Driver Files')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('other'),
                        TextInput::make('payload.specs.outside_diameter')
                            ->label('Outside diameter'),
                        TextInput::make('payload.specs.mount_diameter')
                            ->label('Mounting diameter'),
                        TextInput::make('payload.specs.depth')
                            ->label('Depth'),
                        KeyValue::make('payload.specs.tsparam')
                            ->label('Thiele/Small parameters')
                            ->addable(false)
                            ->deletable(false)
                            ->editableKeys(false)
                            ->keyLabel('Parameters')
                            ->default([
                                'SPL' => '',
                                'Sd' => '',
                                'Mms' => '',
                                'Cms' => '',
                                'Rms' => '',
                                'Le' => '',
                                'Re' => '',
                                'Bl' => '',
                                'fs' => '',
                                'Qes' => '',
                                'Qms' => '',
                                'Qts' => '',
                                'Vas' => '',
                                'Xmax' => '',
                                'Xmech' => '',
                                'Pe' => '',
                                'Vd' => '',
                                'n0%' => '',
                            ]),
                    ]),
            ]);
    }
};
?>

<div class="p-4">
    <x-card title="Manage Your Drivers" subtitle="Every driver you have added to the library." separator>
        {{ $this->table }}
    </x-card>
</div>
