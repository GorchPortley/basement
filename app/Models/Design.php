<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Design extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['payload'];
    protected $casts = ['payload' => 'array',];

    public function drivers()
    {
        return $this->belongsToMany(Driver::class)
            ->using(DriverDesign::class)
            ->withPivot('payload')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function (Design $design) {
            if (! $driver->owner_id && auth()->check()) {
                $design->owner()->associate(auth()->user());
            }
        });
    }

    public function collaborators()
    {
        return $this->hasMany(DesignCollaborator::class);//All collaborators, type filtering can be done in queries?
    }

    public function designer(): MorphTo
    {
        return $this->morphto();
    }

    public function registerMediaCollections(): void
    {
//
    }
}
