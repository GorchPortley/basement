<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['owner_type', 'owner_id', 'payload'])]
class Component extends Model
{
   use InteractsWithMedia;
   public function designs()
    {
    return $this->belongsToMany(Design::class)
        ->using(ComponentDesign::class)
        ->withPivot('payload');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
