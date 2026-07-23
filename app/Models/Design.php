<?php

namespace App\Models;

use App\Enums\DesignAccess;
use App\Enums\DesignCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A published speaker design — the central "product" of the library.
 *
 * Owned polymorphically (a User today, potentially an organisation later).
 * Browse/filter fields are real columns; flexible metadata (bill of materials,
 * extra specs) lives in `payload`. Files live in Spatie media collections.
 */
class Design extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'name',
        'slug',
        'summary',
        'description',
        'category',
        'access',
        'price',
        'build_cost',
        'impedance',
        'power',
        'official',
        'active',
        'forum_slug',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'category' => DesignCategory::class,
            'access' => DesignAccess::class,
            'price' => 'decimal:2',
            'build_cost' => 'decimal:2',
            'official' => 'boolean',
            'active' => 'boolean',
            'payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Design $design): void {
            if (blank($design->slug) && filled($design->name)) {
                $design->slug = static::uniqueSlug($design->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'design';
        $slug = $base;
        $n = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /** The owner / primary designer (polymorphic; a User for now). */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /** Components used by this design, with crossover/enclosure data on the pivot. */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class)
            ->using(ComponentDesign::class)
            ->withPivot(['id', 'position', 'quantity', 'low_frequency', 'high_frequency', 'air_volume', 'payload'])
            ->withTimestamps();
    }

    /** Raw pivot rows — useful when you need per-placement measured media. */
    public function placements(): HasMany
    {
        return $this->hasMany(ComponentDesign::class);
    }

    /** Users credited as collaborators on this design. */
    public function collaborators(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'collaborator', 'collaborator_design')
            ->using(CollaboratorDesign::class)
            ->withPivot(['id', 'payload'])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('card')->singleFile();  // hero / listing image
        $this->addMediaCollection('frd');                 // frequency response measurements (.frd)
        $this->addMediaCollection('enclosure');           // cabinet / CAD files
        $this->addMediaCollection('electronic');          // crossover / wiring files
        $this->addMediaCollection('other');               // anything else bundled with the design
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes & helpers
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOfficial(Builder $query): Builder
    {
        return $query->where('official', true);
    }

    /** Free-text-ish filter used by the public index (no Scout dependency). */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), function (Builder $q) use ($term): void {
            $like = '%'.$term.'%';
            $q->where(fn (Builder $w) => $w
                ->where('name', 'like', $like)
                ->orWhere('summary', 'like', $like)
                ->orWhere('category', 'like', $like));
        });
    }

    /** Bill of materials rows stored in the payload. */
    public function billOfMaterials(): array
    {
        return $this->payload['bill_of_materials'] ?? [];
    }
}
