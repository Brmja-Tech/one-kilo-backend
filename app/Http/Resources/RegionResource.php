<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'governorate_id' => $this->governorate_id,
            'name' => $this->name,
            'shipping_price' => round((float) $this->shipping_price, 2),
            'status' => (bool) $this->status,
        ];
    }
}
