<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unitPrice(): float
    {
        $product = $this->relationLoaded('product') ? $this->product : $this->product()->first();

        return $product?->priceAfterDiscount() ?? 0.0;
    }

    public function lineTotal(): float
    {
        return round($this->unitPrice() * $this->quantity, 2);
    }
}
