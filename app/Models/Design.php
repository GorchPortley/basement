<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Design extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The kinds of speaker a design can be.
     * Keys are stored in payload.meta.type, values are what the user sees.
     */
    public const TYPES = [
        'full_range' => 'Full Range',
        'two_way' => '2-Way',
        'three_way' => '3-Way',
        'four_way_plus' => '4-Way+',
        'subwoofer' => 'Subwoofer',
        'other' => 'Other',
    ];

    protected $fillable = ['payload', 'active', 'owner_id', 'owner_type'];

    protected $casts = [
        'payload' => 'array',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Design $design) {
            if (! $design->owner_id && Auth::check()) {
                $design->owner()->associate(Auth::user());
            }
        });
    }

    /**
     * Whoever created the design. Morphed so it does not have to be a User.
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The drivers used in this design.
     * The pivot row holds the per-design details (position, files, notes).
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'driver_design')
            ->using(DriverDesign::class)
            ->withPivot('payload')
            ->withTimestamps();
    }

    /**
     * The same pivot rows, but as plain models.
     * The design form edits these directly, because each row owns its own
     * uploads and a "has many" is what Filament's repeater expects.
     */
    public function driverDesigns(): HasMany
    {
        return $this->hasMany(DriverDesign::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(CollaboratorDesign::class);
    }

    /**
     * Human readable type, e.g. "3-Way" instead of "three_way".
     */
    public function typeLabel(): string
    {
        $type = data_get($this->payload, 'meta.type');

        return self::TYPES[$type] ?? 'Unspecified';
    }

    /**
     * Read one row out of the key/value specs the builder filled in.
     * The keys are free text, so anything missing falls back to a dash.
     */
    public function spec(string $key, string $default = '—'): string
    {
        $value = data_get($this->payload, 'specs.'.$key);

        return filled($value) ? (string) $value : $default;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('display_img');
        $this->addMediaCollection('frd');
        $this->addMediaCollection('electronics');
        $this->addMediaCollection('enclosure');
        $this->addMediaCollection('other');
    }
}
