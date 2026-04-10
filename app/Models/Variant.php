<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Variant extends Model
{
    use HasTranslations;

    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'key',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VariantItem::class, 'variant_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('status', true);
    }
}
