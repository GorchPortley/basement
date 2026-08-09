<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * One driver, as it is used inside one design.
 *
 * Everything that is true of the driver *in this design* (where it sits, the
 * in-box measurements, the builder's notes) lives here rather than on the
 * driver itself, because the same driver behaves differently in every box.
 */
class DriverDesign extends Pivot implements HasMedia
{
    use InteractsWithMedia;

    /**
     * Where the driver sits in the design.
     * Keys are stored in payload.position, values are what the user sees.
     */
    public const POSITIONS = [
        'lf' => 'Low Frequency',
        'mf' => 'Mid Frequency',
        'hf' => 'High Frequency',
    ];

    protected $table = 'driver_design';

    /**
     * Pivot models normally have no key of their own, but this table was made
     * with an auto-incrementing id so each row can own uploads.
     */
    public $incrementing = true;

    protected $fillable = ['design_id', 'driver_id', 'payload'];

    protected $casts = [
        'payload' => 'array',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    /**
     * Human readable position, e.g. "Low Frequency" instead of "lf".
     */
    public function positionLabel(): string
    {
        $position = data_get($this->payload, 'position');

        return self::POSITIONS[$position] ?? 'Unspecified';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('zma');
        $this->addMediaCollection('frd');
        $this->addMediaCollection('other');
    }
}
