<?php

namespace App\Models;

use App\Support\MediaLibrary\DriverPathGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

class Driver extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['payload', 'active'];

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

    protected static function booting(): void
    {
        PathGeneratorFactory::setCustomPathGenerators(static::class, DriverPathGenerator::class);
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
        return $this->morphto();
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
