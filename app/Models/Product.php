<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use Sluggable , HasTranslations;

    public $translatable = [
        'name',
        'short_description',
        'description',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'image',
        'price',
        'discount_type',
        'discount_value',
        'discount_starts_at',
        'discount_ends_at',
        'sku',
        'stock',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_starts_at' => 'datetime',
            'discount_ends_at' => 'datetime',
            'stock' => 'integer',
            'is_featured' => 'boolean',
            'status' => 'boolean',
        ];
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function priceBeforeDiscount(): float
    {
        return round((float) $this->price, 2);
    }

    public function hasActiveDiscount(?CarbonInterface $moment = null): bool
    {
        $moment ??= now();
        $price = $this->priceBeforeDiscount();
        $discountValue = (float) ($this->discount_value ?? 0);

        if (! in_array($this->discount_type, ['amount', 'percentage'], true)) {
            return false;
        }

        if ($discountValue <= 0 || $price <= 0) {
            return false;
        }

        if ($this->discount_type === 'percentage' && $discountValue > 100) {
            return false;
        }

        if ($this->discount_starts_at && $moment->lt($this->discount_starts_at)) {
            return false;
        }

        if ($this->discount_ends_at && $moment->gt($this->discount_ends_at)) {
            return false;
        }

        return true;
    }

    public function priceAfterDiscount(?CarbonInterface $moment = null): float
    {
        $price = $this->priceBeforeDiscount();

        if (! $this->hasActiveDiscount($moment)) {
            return $price;
        }

        $discountValue = (float) $this->discount_value;

        if ($this->discount_type === 'amount') {
            return round(max($price - $discountValue, 0), 2);
        }

        return round(max($price - ($price * $discountValue / 100), 0), 2);
    }

    public function activeDiscountAmount(?CarbonInterface $moment = null): float
    {
        return round(max($this->priceBeforeDiscount() - $this->priceAfterDiscount($moment), 0), 2);
    }

    public function discountPercentage(?CarbonInterface $moment = null): ?float
    {
        if (! $this->hasActiveDiscount($moment)) {
            return null;
        }

        if ($this->discount_type === 'percentage') {
            return round((float) $this->discount_value, 2);
        }

        $price = $this->priceBeforeDiscount();

        if ($price <= 0) {
            return null;
        }

        return round(($this->activeDiscountAmount($moment) / $price) * 100, 2);
    }
}
