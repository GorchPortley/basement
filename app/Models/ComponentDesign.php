<?php

namespace App\Models;

use App\Enums\ComponentPosition;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Pivot describing how a Component is deployed inside a Design: its crossover
 * position, quantity, corner frequencies and enclosure volume. Carries its own
 * media so a designer can attach *measured* (in-situ) FRD/ZMA data that differs
 * from the component's factory data.
 */
class ComponentDesign extends Pivot implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'component_design';

    /** This pivot has its own auto-incrementing id (needed to own media). */
    public $incrementing = true;

    protected $fillable = [
        'design_id',
        'component_id',
        'position',
        'quantity',
        'low_frequency',
        'high_frequency',
        'air_volume',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'position' => ComponentPosition::class,
            'quantity' => 'integer',
            'low_frequency' => 'decimal:2',
            'high_frequency' => 'decimal:2',
            'air_volume' => 'decimal:2',
            'payload' => 'array',
        ];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('frequency'); // measured frequency response for this placement
        $this->addMediaCollection('impedance'); // measured impedance for this placement
        $this->addMediaCollection('other');
    }
}
