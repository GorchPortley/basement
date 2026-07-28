<?php

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

new class extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public ?array $miniData = [];

    public array $dumped = [];

    public function mount(): void
    {
        $this->mini->fill([
            'driver_name' => 'Dayton RS180-8',
            'enclosure' => 'sealed',
            'is_published' => true,
        ]);

        $this->form->fill([
            'driver_name' => 'Dayton RS180-8',
            'enclosure' => 'sealed',
            'tuning_hz' => 42,
            'notes' => 'Baffle step correction still needs measuring.',
            'is_published' => true,
            'accent' => '#6366f1',
            'tags' => ['two-way', 'bookshelf'],
        ]);
    }

    /**
     * Three controls only — the direct counterparts to the maryUI row.
     */
    public function mini(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('driver_name')
                    ->label('Driver name')
                    ->placeholder('Dayton RS180-8'),

                Select::make('enclosure')
                    ->label('Enclosure type')
                    ->options([
                        'sealed' => 'Sealed',
                        'ported' => 'Ported',
                    ]),

                Checkbox::make('is_published')
                    ->label('Published'),
            ])
            ->statePath('miniData');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Showcase')
                    ->tabs([

                        // ── Text entry ─────────────────────────────────
                        Tabs\Tab::make('Text')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('driver_name')
                                        ->label('Driver name')
                                        ->required()
                                        ->helperText('Manufacturer and model.'),

                                    TextInput::make('sensitivity')
                                        ->label('Sensitivity')
                                        ->numeric()
                                        ->suffix('dB')
                                        ->hint('1W / 1m'),

                                    TextInput::make('shop_url')
                                        ->label('Product page')
                                        ->url()
                                        ->prefixIcon('heroicon-o-link')
                                        ->placeholder('https://'),

                                    TextInput::make('price')
                                        ->label('Price')
                                        ->numeric()
                                        ->prefix('$')
                                        ->required(),

                                    TextInput::make('api_key')
                                        ->label('API key')
                                        ->password()
                                        ->revealable(),

                                    TextInput::make('serial')
                                        ->label('Serial')
                                        ->disabled()
                                        ->default('SD-0001-A'),
                                ]),

                                Textarea::make('notes')
                                    ->label('Build notes')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        // ── Choices ────────────────────────────────────
                        Tabs\Tab::make('Choices')
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('enclosure')
                                        ->label('Enclosure type')
                                        ->options([
                                            'sealed' => 'Sealed',
                                            'ported' => 'Ported',
                                            'passive' => 'Passive radiator',
                                            'horn' => 'Horn',
                                        ])
                                        ->required(),

                                    Select::make('drivers')
                                        ->label('Drivers used')
                                        ->multiple()
                                        ->searchable()
                                        ->options([
                                            'rs180' => 'Dayton RS180-8',
                                            'rs28' => 'Dayton RS28F-4',
                                            'sb17' => 'SB Acoustics SB17NRXC',
                                            'xt25' => 'Peerless XT25SC90',
                                        ]),
                                ]),

                                Radio::make('crossover_order')
                                    ->label('Crossover order')
                                    ->options([
                                        '1' => 'First order',
                                        '2' => 'Second order',
                                        '4' => 'Fourth order',
                                    ])
                                    ->inline(),

                                CheckboxList::make('measurements')
                                    ->label('Measurements included')
                                    ->columns(2)
                                    ->options([
                                        'fr' => 'Frequency response',
                                        'imp' => 'Impedance',
                                        'thd' => 'Distortion',
                                        'csd' => 'Waterfall',
                                    ]),

                                ToggleButtons::make('difficulty')
                                    ->label('Build difficulty')
                                    ->inline()
                                    ->options([
                                        'easy' => 'Beginner',
                                        'medium' => 'Intermediate',
                                        'hard' => 'Advanced',
                                    ])
                                    ->colors([
                                        'easy' => 'success',
                                        'medium' => 'warning',
                                        'hard' => 'danger',
                                    ]),

                                Grid::make(2)->schema([
                                    Toggle::make('is_published')
                                        ->label('Published')
                                        ->helperText('Visible on the marketplace.'),

                                    Checkbox::make('accepts_tips')
                                        ->label('Accept tips on this design'),
                                ]),
                            ]),

                        // ── Dates, color, files ────────────────────────
                        Tabs\Tab::make('Pickers')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Grid::make(3)->schema([
                                    DatePicker::make('released_on')
                                        ->label('Release date'),

                                    DateTimePicker::make('contest_closes_at')
                                        ->label('Contest closes')
                                        ->seconds(false),

                                    TimePicker::make('listening_session')
                                        ->label('Session time'),
                                ]),

                                Grid::make(2)->schema([
                                    ColorPicker::make('accent')
                                        ->label('Accent color'),

                                    TagsInput::make('tags')
                                        ->label('Tags')
                                        ->placeholder('Add a tag'),
                                ]),

                                FileUpload::make('cabinet_plans')
                                    ->label('Cabinet plans')
                                    ->helperText('Needs a writable disk — comment this field out if storage is not configured yet.')
                                    ->columnSpanFull(),
                            ]),

                        // ── Rich text ──────────────────────────────────
                        Tabs\Tab::make('Editors')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                RichEditor::make('description')
                                    ->label('Description')
                                    ->columnSpanFull(),

                                MarkdownEditor::make('assembly_guide')
                                    ->label('Assembly guide')
                                    ->columnSpanFull(),
                            ]),

                        // ── Repeating structures ───────────────────────
                        Tabs\Tab::make('Repeaters')
                            ->icon('heroicon-o-square-3-stack-3d')
                            ->schema([
                                Repeater::make('bill_of_materials')
                                    ->label('Bill of materials')
                                    ->collapsible()
                                    ->cloneable()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('part')->label('Part'),
                                        TextInput::make('qty')->label('Qty')->numeric(),
                                        TextInput::make('cost')->label('Cost')->prefix('$'),
                                    ])
                                    ->columnSpanFull(),

                                KeyValue::make('specs')
                                    ->label('Specifications')
                                    ->keyLabel('Spec')
                                    ->valueLabel('Value')
                                    ->columnSpanFull(),

                                Builder::make('page_blocks')
                                    ->label('Design page blocks')
                                    ->blocks([
                                        Builder\Block::make('heading')
                                            ->icon('heroicon-o-bars-3-bottom-left')
                                            ->schema([
                                                TextInput::make('text')->label('Heading'),
                                            ]),
                                        Builder\Block::make('paragraph')
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                Textarea::make('text')->label('Body'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        // ── Containers ─────────────────────────────────
                        Tabs\Tab::make('Layout')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Section::make('Collapsible section')
                                    ->description('Sections carry a heading, description, border and padding — a big chunk of the visual surface you are trying to match.')
                                    ->icon('heroicon-o-speaker-wave')
                                    ->collapsible()
                                    ->schema([
                                        TextInput::make('section_field')->label('Field inside a section'),
                                    ]),

                                Section::make('Aside section')
                                    ->description('Heading in a column beside the fields.')
                                    ->aside()
                                    ->schema([
                                        TextInput::make('aside_field')->label('Field inside an aside section'),
                                    ]),

                                Fieldset::make('Fieldset')
                                    ->schema([
                                        TextInput::make('fieldset_a')->label('First'),
                                        TextInput::make('fieldset_b')->label('Second'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->dumped = $this->form->getState();
    }

    public function resetForm(): void
    {
        $this->dumped = [];
        $this->form->fill();
    }
}; ?>

<div class="space-y-8">

    {{-- Reference row: the same three controls, maryUI on the left, Filament on the right. --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h2 class="card-title">Side by side</h2>
            <p class="text-sm opacity-70">
                Same three controls in both libraries. Check radius, border color, label weight,
                height and focus ring here first — these are the tells.
            </p>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-4">
                    <div class="badge badge-primary">maryUI</div>
                    <x-input label="Driver name" placeholder="Dayton RS180-8" />
                    <x-select
                        label="Enclosure type"
                        :options="[['id' => 'sealed', 'name' => 'Sealed'], ['id' => 'ported', 'name' => 'Ported']]"
                    />
                    <x-checkbox label="Published" />
                    <x-button label="Save design" class="btn-primary" />
                </div>

                <div class="space-y-4">
                    <div class="badge badge-secondary">Filament</div>
                    {{ $this->mini }}
                </div>
            </div>
        </div>
    </div>

    {{-- The full Filament surface. --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h2 class="card-title">Filament fields</h2>

            {{ $this->form }}

            <div class="card-actions mt-6 justify-end">
                <x-button label="Reset" wire:click="resetForm" />
                <x-button label="Save design" class="btn-primary" wire:click="save" spinner="save" />
            </div>
        </div>
    </div>

    {{-- State dump, so you can confirm the form actually round-trips. --}}
    @if (filled($dumped))
        <div class="mockup-code text-xs">
            <pre><code>{{ json_encode($dumped, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </div>
    @endif
</div>
