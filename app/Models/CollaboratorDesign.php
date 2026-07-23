<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Morph pivot linking a Design to a collaborator (a User today). The `payload`
 * holds the collaborator's role and any credit/permission notes so the model
 * stays flexible without schema churn.
 */
class CollaboratorDesign extends MorphPivot
{
    protected $table = 'collaborator_design';

    public $incrementing = true;

    protected $fillable = [
        'collaborator_id',
        'collaborator_type',
        'design_id',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function collaborator(): MorphTo
    {
        return $this->morphTo();
    }

    /** Convenience accessor for the role stored in the payload. */
    public function role(): ?string
    {
        return $this->payload['role'] ?? null;
    }
}
