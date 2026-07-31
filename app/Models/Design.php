<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['owner_type', 'owner_id', 'payload'])]
class Design extends Model
{
    use InteractsWithMedia;
    public function components()
    {
        return $this->belongsToMany(Driver::class)
            ->using(DriverDesign::class)
            ->withPivot('payload')
            ->withTimestamps();
    }

    public function collaborators()
    {
        return $this->hasMany(DesignCollaborator::class);//All collaborators, type filtering can be done in queries?
    }

    public function designer()
    {
        return $this->belongsTo(User::class);
    }
}
