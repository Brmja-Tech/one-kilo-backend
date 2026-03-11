<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
    ];

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function itemsCount(): int
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return (int) $items->sum('quantity');
    }

    public function subtotal(): float
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        return round($items->sum(fn (CartItem $item) => $item->lineTotal()), 2);
    }

    public function couponDiscount(): float
    {
        $coupon = $this->relationLoaded('coupon') ? $this->coupon : $this->coupon()->first();

        if (! $coupon) {
            return 0.0;
        }

        return $coupon->calculateDiscount($this->subtotal());
    }

    public function total(): float
    {
        return round(max($this->subtotal() - $this->couponDiscount(), 0), 2);
    }
}
