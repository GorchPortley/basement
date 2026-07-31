<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;




class Driver extends Model implements HasMedia
{
   use InteractsWithMedia;
    protected $fillable = ['payload'];
    protected $casts = ['payload' => 'array',];

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
        $this->addMediaCollection('images');
        $this->addMediaCollection('frd');
        $this->addMediaCollection('zma');
    }
}
