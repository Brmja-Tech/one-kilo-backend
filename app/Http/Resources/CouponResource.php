<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'value' => round((float) $this->value, 2),
            'min_order_amount' => $this->min_order_amount !== null ? round((float) $this->min_order_amount, 2) : null,
            'max_discount_amount' => $this->max_discount_amount !== null ? round((float) $this->max_discount_amount, 2) : null,
            'status' => (bool) $this->status,
        ];
    }
}
