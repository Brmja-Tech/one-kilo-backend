<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSku extends Model
{
    protected $fillable = [
        'product_id',
        'signature',
        'sku',
        'image',
        'price',
        'quantity',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductSkuItem::class, 'product_sku_id');
    }

    public static function signatureFromAttributes(array $attributes): string
    {
        $pairs = collect($attributes)
            ->map(function (array $attribute): array {
                return [
                    'variant_id' => (int) Arr::get($attribute, 'variant_id'),
                    'variant_item_id' => (int) Arr::get($attribute, 'variant_item_id'),
                ];
            })
            ->filter(fn (array $attribute) => $attribute['variant_id'] > 0 && $attribute['variant_item_id'] > 0)
            ->sortBy('variant_id')
            ->map(fn (array $attribute) => $attribute['variant_id'] . ':' . $attribute['variant_item_id'])
            ->values()
            ->all();

        return implode('|', $pairs);
    }

    public function label(): ?string
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with(['variant', 'item'])->get();

        $label = $items
            ->filter(fn (ProductSkuItem $item) => $item->variant && $item->item)
            ->sortBy(fn (ProductSkuItem $item) => [$item->variant?->sort_order ?? 0, $item->variant_id])
            ->map(fn (ProductSkuItem $item) => $item->variant->name . ': ' . $item->item->name)
            ->implode(', ');

        return $label !== '' ? $label : null;
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_sku_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_sku_id');
    }

    public function priceBeforeDiscount(): float
    {
        return round((float) $this->price, 2);
    }

    public function hasActiveDiscount(?CarbonInterface $moment = null): bool
    {
        $product = $this->relationLoaded('product') ? $this->product : $this->product()->first();

        return $product?->discountSettingsAreActive($moment) ?? false;
    }

    public function priceAfterDiscount(?CarbonInterface $moment = null): float
    {
        $product = $this->relationLoaded('product') ? $this->product : $this->product()->first();
        $price = $this->priceBeforeDiscount();

        return $product
            ? $product->priceAfterDiscountForPrice($price, $moment)
            : $price;
    }

    public function activeDiscountAmount(?CarbonInterface $moment = null): float
    {
        $product = $this->relationLoaded('product') ? $this->product : $this->product()->first();

        return $product
            ? $product->discountAmountForPrice($this->priceBeforeDiscount(), $moment)
            : 0.0;
    }
}
