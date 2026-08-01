<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Driver extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['payload'];

    protected $casts = ['payload' => 'array'];

    public static function getBrands(): array
    {
        return DB::table('drivers')->pluck('payload')
            ->map(fn ($p) => data_get(json_decode($p, true), 'meta.brand'))
            ->filter()
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
            if (! $driver->owner_id && auth()->check()) {
                $driver->owner()->associate(auth()->user());
            }
        });
    }

    public function owner(): MorphTo
    {
        return $this->morphto();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('datasheet')->singleFile();
        $this->addMediaCollection('prod_img');
        $this->addMediaCollection('description_img');
        $this->addMediaCollection('frd');
        $this->addMediaCollection('zma');
    }
}
