<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image ? asset($this->image) : '',
            'category' => $this->whenLoaded('category', fn() => new CategoryResource($this->category)),
            'has_variants' => $this->hasVariants(),
            'requires_variant_selection' => $this->requiresVariantSelection(),
            'sold_quantity' => $this->when(isset($this->sold_quantity), (int) $this->sold_quantity),
            'stock' => $this->when(! $this->hasVariants(), (int) $this->stock),
            'status' => (bool) $this->status,
            'price_before_discount' => $this->when(! $this->hasVariants(), fn () => $this->asDouble($this->priceBeforeDiscount())),
            'price' => $this->when(! $this->hasVariants(), fn () => $this->asDouble($this->priceAfterDiscount())),
            'price_after_discount' => $this->when(! $this->hasVariants(), fn () => $this->asDouble($this->priceAfterDiscount())),
            'has_active_discount' => $this->when(! $this->hasVariants(), fn () => $this->hasActiveDiscount()),
            'price_range' => $this->when($this->hasVariants(), [
                'min' => $this->asDouble($this->minVariantPrice()),
                'max' => $this->asDouble($this->maxVariantPrice()),
            ]),
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }

    private function asDouble(?float $value): ?float
    {
        return $value === null ? null : (double) $value;
    }
}
