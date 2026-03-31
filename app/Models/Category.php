<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use Sluggable, HasTranslations;

    public const COLOR_REGEX = '/^0x[0-9A-F]{8}$/i';

    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'color',
        'image',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function color(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => static::normalizeColor($value),
        );
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with(['parent:id,slug', 'childrenRecursive']);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)
            ->orderBy('name');
    }

    public static function colorValidationRules(bool $required = true): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'regex:' . self::COLOR_REGEX,
        ];
    }

    public static function normalizeColor(?string $color): ?string
    {
        if ($color === null) {
            return null;
        }

        $normalized = trim($color);

        if (! preg_match(self::COLOR_REGEX, $normalized)) {
            throw new InvalidArgumentException('Category color must use the 0xAARRGGBB format.');
        }

        return '0x' . strtoupper(substr($normalized, 2));
    }
}
