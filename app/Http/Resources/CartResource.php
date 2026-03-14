<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this->id,
            'coupon_id' => $this->coupon_id,
            'coupon' => $this->whenLoaded('coupon', fn() => $this->coupon ? new CouponResource($this->coupon) : null),
            'items_count' => $this->itemsCount(),
            'subtotal' => $this->subtotal(),
            'discount_total' => $this->couponDiscount(),
            'total' => $this->total(),
            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
