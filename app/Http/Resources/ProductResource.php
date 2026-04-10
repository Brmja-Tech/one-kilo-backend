<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'image' => $this->image ? asset($this->image) : '',
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'category' => $this->whenLoaded('category', fn() => new CategoryResource($this->category)),
            'has_variants' => $this->hasVariants(),
            'requires_variant_selection' => $this->requiresVariantSelection(),
            'stock' => $this->when(! $this->hasVariants(), (int) $this->stock),
            'status' => (int) $this->status,
            'is_featured' => (int) $this->is_featured,
            'price_before_discount' => $this->when(! $this->hasVariants(), $this->priceBeforeDiscount()),
            'price_after_discount' => $this->when(! $this->hasVariants(), $this->priceAfterDiscount()),
            'discount_type' => $this->when(! $this->hasVariants(), $this->discount_type),
            'discount_value' => $this->when(! $this->hasVariants(), $this->discount_value !== null ? round((float) $this->discount_value, 2) : null),
            'discount_starts_at' => $this->when(! $this->hasVariants(), $this->discount_starts_at?->toDateTimeString()),
            'discount_ends_at' => $this->when(! $this->hasVariants(), $this->discount_ends_at?->toDateTimeString()),
            'has_active_discount' => $this->when(! $this->hasVariants(), $this->hasActiveDiscount()),
            'final_price' => $this->when(! $this->hasVariants(), $this->priceAfterDiscount()),
            'discount_percentage' => $this->when(! $this->hasVariants(), $this->discountPercentage()),
            'price_range' => $this->when($this->hasVariants(), [
                'min' => $this->minVariantPrice(),
                'max' => $this->maxVariantPrice(),
            ]),
            'skus' => $this->when($this->hasVariants() && $this->relationLoaded('activeSkus'), ProductSkuResource::collection($this->activeSkus)),
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }
}
