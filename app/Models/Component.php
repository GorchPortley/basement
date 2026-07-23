<?php

namespace App\Models;

use App\Enums\ComponentCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A driver / part that designs reference (woofer, tweeter, compression driver…).
 *
 * Components are *referenced*, never sold on-site — `link` points out to a
 * retailer (affiliate). Manufacturer-supplied data (Thiele-Small parameters,
 * factory FRD/ZMA) makes this a verifiable driver database; the `official`
 * flag means "verified", never "paid placement".
 */
class Component extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'brand',
        'model',
        'slug',
        'category',
        'size',
        'impedance',
        'power',
        'price',
        'link',
        'summary',
        'description',
        'official',
        'active',
        'forum_slug',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'category' => ComponentCategory::class,
            'size' => 'decimal:2',
            'price' => 'decimal:2',
            'official' => 'boolean',
            'active' => 'boolean',
            'payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Component $component): void {
            if (blank($component->slug)) {
                $component->slug = static::uniqueSlug(trim("{$component->brand} {$component->model}"));
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'component';
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

    /** Display name "Brand Model" (there is no `name` column). */
    protected function name(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->brand} {$this->model}"));
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function designs(): BelongsToMany
    {
        return $this->belongsToMany(Design::class)
            ->using(ComponentDesign::class)
            ->withPivot(['id', 'position', 'quantity', 'low_frequency', 'high_frequency', 'air_volume', 'payload'])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('card')->singleFile();  // listing image
        $this->addMediaCollection('frequency');           // factory frequency response (.frd)
        $this->addMediaCollection('impedance');           // factory impedance (.zma)
        $this->addMediaCollection('other');               // datasheets, drawings, etc.
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
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

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), function (Builder $q) use ($term): void {
            $like = '%'.$term.'%';
            $q->where(fn (Builder $w) => $w
                ->where('brand', 'like', $like)
                ->orWhere('model', 'like', $like)
                ->orWhere('category', 'like', $like));
        });
    }

    /** Thiele-Small / factory parameters stored in the payload. */
    public function factorySpecs(): array
    {
        return $this->payload['factory_specs'] ?? [];
    }
}
