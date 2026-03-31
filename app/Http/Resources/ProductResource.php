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
            'stock' => (int) $this->stock,
            'status' => (int) $this->status,
            'is_featured' => (int) $this->is_featured,
            'price_before_discount' => $this->priceBeforeDiscount(),
            'price_after_discount' => $this->priceAfterDiscount(),
            'discount_type' => $this->discount_type ,
            'discount_value' => $this->discount_value !== null ? round((float) $this->discount_value, 2) : null,
            'discount_starts_at' => $this->discount_starts_at?->toDateTimeString(),
            'discount_ends_at' => $this->discount_ends_at?->toDateTimeString(),
            'has_active_discount' => $this->hasActiveDiscount(),
            'final_price' => $this->priceAfterDiscount(),
            'discount_percentage' => $this->discountPercentage(),
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }
}
