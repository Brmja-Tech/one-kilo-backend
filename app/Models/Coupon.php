<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'usage_limit' => 'integer',
            'usage_limit_per_user' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_usages')
            ->withTimestamps()
            ->withPivot(['used_at', 'order_id', 'discount_amount']);
    }

    public function isActive(?CarbonInterface $moment = null): bool
    {
        $moment ??= now();

        if (! $this->status) {
            return false;
        }

        if ($this->starts_at && $moment->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $moment->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function hasRemainingUsage(): bool
    {
        return $this->usage_limit === null || $this->used_count < $this->usage_limit;
    }

    public function canApplyToSubtotal(float $subtotal): bool
    {
        if (! $this->isActive() || ! $this->hasRemainingUsage()) {
            return false;
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->canApplyToSubtotal($subtotal) || $subtotal <= 0) {
            return 0.0;
        }

        $value = (float) $this->value;
        $discount = $this->type === 'amount'
            ? min($subtotal, $value)
            : ($subtotal * $value / 100);

        if ($this->type === 'percentage' && $this->max_discount_amount !== null) {
            $discount = min($discount, (float) $this->max_discount_amount);
        }

        return round(max($discount, 0), 2);
    }
}
