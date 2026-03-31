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
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'sold_quantity' => $this->when(isset($this->sold_quantity), (int) $this->sold_quantity),
            'stock' => (int) $this->stock,
            'status' => (bool) $this->status,
            'price_before_discount' => $this->priceBeforeDiscount(),
            'price_after_discount' => $this->priceAfterDiscount(),
            'has_active_discount' => $this->hasActiveDiscount(),
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }
}
