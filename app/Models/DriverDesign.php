<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\InteractsWithMedia;

class DriverDesign extends Pivot
{
    use InteractsWithMedia;

    protected $fillable = ['design_id', 'component_id', 'payload'];

    protected $table = 'component_design';

    protected $casts = [
        'payload' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }
}
