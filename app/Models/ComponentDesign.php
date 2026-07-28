<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


#[Fillable(['design_id', 'component_id', 'payload'])]
class ComponentDesign extends Pivot
{
    use InteractsWithMedia;

    protected $table = 'component_design';

    protected $casts = [
        'payload' => 'array',
    ];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }
}
