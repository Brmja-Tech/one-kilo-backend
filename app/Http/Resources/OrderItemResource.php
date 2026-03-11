<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_image' => $this->product_image ? asset($this->product_image) : '',
            'unit_price' => round((float) $this->unit_price, 2),
            'quantity' => (int) $this->quantity,
            'line_total' => round((float) $this->line_total, 2),
        ];
    }
}
