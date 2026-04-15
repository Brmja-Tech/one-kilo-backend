<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'image'      => asset($this->image),
            'full_name'       => $this->full_name,
            'email'      => $this->email ?? '',
            'phone'      => $this->phone,
            'vehicle_type'      => $this->vehicle_type,
            'national_id_image'      => asset($this->national_id_image),
            'license_image'      => asset($this->license_image),
            'vehicle_license_image'      => asset($this->vehicle_license_image),
            'status'      => $this->status,
        ];
    }
}
