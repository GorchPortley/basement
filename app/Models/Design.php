<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Design extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['payload'];

    protected $casts = ['payload' => 'array'];

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
            if (! $design->owner_id && Auth::check()) {
                $design->owner()->associate(Auth::user());
            }
        });
    }

    public function collaborators()
    {
        return $this->hasMany(CollaboratorDesign::class); // All collaborators, type filtering can be done in queries?
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
