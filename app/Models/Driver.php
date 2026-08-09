<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Driver extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The kinds of driver we accept.
     * Keys are stored in payload.meta.type, values are what the user sees.
     */
    public const TYPES = [
        'subwoofer' => 'Subwoofer',
        'woofer' => 'Woofer',
        'midrange' => 'Midrange',
        'tweeter' => 'Tweeter',
        'passive_radiator' => 'Passive Radiator',
        'compression_driver' => 'Compression Driver',
        'horn' => 'Horn',
        'waveguide' => 'Waveguide',
    ];

    protected $fillable = ['payload', 'active', 'owner_id', 'owner_type'];

    protected $casts = [
        'payload' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Every brand already in the library, so the driver form can suggest them
     * instead of letting everyone spell "Dayton Audio" their own way.
     *
     * @return array<int, string>
     */
    public static function getBrands(): array
    {
        return static::query()
            ->pluck('payload')
            ->map(fn ($payload) => data_get($payload, 'meta.brand'))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function designs(): BelongsToMany
    {
        return $this->belongsToMany(Design::class, 'driver_design')
            ->using(DriverDesign::class)
            ->withPivot('payload')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function (Driver $driver) {
            if (! $driver->owner_id && Auth::check()) {
                $driver->owner()->associate(Auth::user());
            }
        });
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * "Dayton Audio RS180-8" — used anywhere a driver needs a one line name.
     */
    public function displayName(): string
    {
        $brand = data_get($this->payload, 'meta.brand');
        $model = data_get($this->payload, 'meta.model');

        return trim($brand.' '.$model) ?: 'Untitled driver';
    }

    /**
     * Human readable type, e.g. "Passive Radiator" instead of "passive_radiator".
     */
    public function typeLabel(): string
    {
        $type = data_get($this->payload, 'meta.type');

        return self::TYPES[$type] ?? 'Unspecified';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('datasheet');
        $this->addMediaCollection('prod_img');
        $this->addMediaCollection('description_img');
        $this->addMediaCollection('frd');
        $this->addMediaCollection('zma');
        $this->addMediaCollection('other');
    }
}
