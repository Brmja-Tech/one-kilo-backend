<?php

namespace App\Http\Resources;

use App\Models\ProductSkuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSkuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $basePrice = $this->priceBeforeDiscount();
        $finalPrice = $this->priceAfterDiscount();

        return [
            'id' => (int) $this->id,
            'attributes' => $this->attributesPayload(),
            'price_before_discount' => (double) $basePrice,
            'price' => (double) $finalPrice,
            'discount' => [
                'is_active' => (bool) $this->hasActiveDiscount(),
                'amount' => (float) $this->activeDiscountAmount(),
            ],
            'quantity' => (int) $this->quantity,
            'sku' => $this->sku,
            'image' => $this->image ? asset($this->image) : null,
        ];
    }

    protected function attributesPayload(): array
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with(['variant', 'item'])->get();

        return $items
            ->filter(fn (ProductSkuItem $item) => $item->variant && $item->item)
            ->sortBy(fn (ProductSkuItem $item) => [$item->variant?->sort_order ?? 0, $item->variant_id])
            ->map(fn (ProductSkuItem $item) => [
                'id' => (int) $item->variant_id,
                'key' => (string) ($item->variant?->key ?? ''),
                'name' => $item->variant?->name,
                'item' => [
                    'id' => (int) $item->variant_item_id,
                    'label' => $item->item?->name,
                    'meta' => $item->item?->meta,
                ],
            ])
            ->values()
            ->all();
    }
}

