<?php

use App\Models\Design;
use App\Models\Driver;
use App\Models\DriverDesign;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
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
     * The list of designs this user owns. Creating, editing and deleting all
     * happen through the table's actions, which reuse designForm() below.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(Design::query()->where('owner_id', auth()->id()))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('payload.meta.title')
                    ->label('Title'),
                TextColumn::make('payload.meta.type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => Design::TYPES[$state] ?? 'Unspecified')
                    ->badge(),
                TextColumn::make('payload.meta.build_cost')
                    ->label('Build cost')
                    ->money('USD'),
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
                EditAction::make('editDesign')
                    ->schema(fn (Schema $schema): Schema => $this->designForm($schema)),
                DeleteAction::make('deleteDesign'),
            ])
            ->toolbarActions([
                CreateAction::make('newDesign')
                    ->label('Add a design')
                    ->schema(fn (Schema $schema): Schema => $this->designForm($schema)),
            ])
            ->emptyStateHeading('No designs yet')
            ->emptyStateDescription('Add a design to start writing up a build.');
    }

    /**
     * The create/edit form. It is handed the schema owned by whichever action
     * opened it, so it must not set its own state path.
     */
    public function designForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Design Information')
                    ->description('What the design is and what it costs to build.')
                    ->schema([
                        Toggle::make('active')
                            ->label('Published')
                            ->helperText('Published designs show up in the public browse page.')
                            ->default(false),
                        TextInput::make('payload.meta.title')
                            ->label('Title')
                            ->required()
                            ->maxLength(120),
                        Select::make('payload.meta.type')
                            ->label('Type')
                            ->options(Design::TYPES)
                            ->required(),
                        TextInput::make('payload.meta.tagline')
                            ->label('Tag line')
                            ->helperText('One short line that sums the design up.')
                            ->maxLength(160),
                        TextInput::make('payload.meta.build_cost')
                            ->label('Build cost')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Roughly what a pair costs to build.'),
                        SpatieMediaLibraryFileUpload::make('display_image')
                            ->label('Display images')
                            ->helperText('The first image is the one shown on the browse card.')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('display_img'),
                    ]),

                Section::make('Descriptions')
                    ->description('Describe the design')
                    ->schema([
                        RichEditor::make('payload.descriptions.summary')
                            ->label('Summary')
                            ->helperText('A short intro shown before the full write up.'),
                        RichEditor::make('payload.descriptions.description')
                            ->label('Full description'),
                    ]),

                Section::make('Files and Specifications')
                    ->description('Everything somebody would need to build it.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('frd')
                            ->label('FRD Previews')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('frd'),
                        SpatieMediaLibraryFileUpload::make('electronics')
                            ->label('Electronics Files')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('electronics'),
                        SpatieMediaLibraryFileUpload::make('enclosure')
                            ->label('Enclosure Files')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('enclosure'),
                        SpatieMediaLibraryFileUpload::make('other')
                            ->label('Other Design Files')
                            ->multiple()
                            ->disk('uploads')
                            ->visibility('public')
                            ->collection('other'),
                        // Free form so builders can add whatever else matters.
                        // The browse card reads the "Frequency Range" row, so
                        // renaming that one means the card falls back to a dash.
                        KeyValue::make('payload.specs')
                            ->label('Specifications')
                            ->keyLabel('Spec')
                            ->valueLabel('Value')
                            ->default([
                                'Power' => '',
                                'Sensitivity' => '',
                                'Frequency Range' => '',
                                'Impedance' => '',
                            ]),
                    ]),

                Section::make('Drivers Used')
                    ->description('Pick drivers from the library and say how each one is used here.')
                    ->schema([
                        // Each row of this repeater is a DriverDesign record, so
                        // the notes and the in-box measurements belong to the
                        // driver *in this design* rather than to the driver.
                        Repeater::make('driverDesigns')
                            ->relationship()
                            ->label('Drivers')
                            ->addActionLabel('Add a driver')
                            // Start empty. Otherwise a blank row is always
                            // there and its required fields block saving.
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => filled($state['driver_id'] ?? null)
                                ? Driver::find($state['driver_id'])?->displayName()
                                : null)
                            ->schema([
                                Select::make('driver_id')
                                    ->label('Driver')
                                    ->options(fn () => Driver::query()
                                        ->where('active', true)
                                        ->get()
                                        ->mapWithKeys(fn (Driver $driver) => [$driver->id => $driver->displayName()])
                                        ->all())
                                    ->searchable()
                                    ->required(),
                                Select::make('payload.position')
                                    ->label('Position')
                                    ->options(DriverDesign::POSITIONS)
                                    ->required(),
                                RichEditor::make('payload.description')
                                    ->label('Notes on this driver'),
                                SpatieMediaLibraryFileUpload::make('zma_in_box')
                                    ->label('In box impedance files (ZMA)')
                                    ->multiple()
                                    ->disk('uploads')
                                    ->visibility('public')
                                    ->collection('zma'),
                                SpatieMediaLibraryFileUpload::make('frd_in_box')
                                    ->label('In box response files (FRD)')
                                    ->multiple()
                                    ->disk('uploads')
                                    ->visibility('public')
                                    ->collection('frd'),
                                SpatieMediaLibraryFileUpload::make('other')
                                    ->label('Other files')
                                    ->multiple()
                                    ->disk('uploads')
                                    ->visibility('public')
                                    ->collection('other'),
                            ]),
                    ]),
            ]);
    }
};
?>

<div class="p-4">
    <x-card title="Manage Your Designs" subtitle="Every design you have written up." separator>
        {{ $this->table }}
    </x-card>
</div>
