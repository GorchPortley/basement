<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MorphPivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['design_id', 'collaborator_id', 'collaborator_type', 'payload'])]
class CollaboratorDesign extends MorphPivot
{
    use InteractsWithMedia;
    protected $table = 'collaborator_design';

    protected $casts = [
        'payload' => 'array',
    ];

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function collaborator()
    {
        return $this->morphTo();
    }    
}
