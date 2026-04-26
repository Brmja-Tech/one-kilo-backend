<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderLocationResource extends OrderResource
{
    public function toArray(Request $request): array
    {
        return [

            'lat' => $this->delivery ? $this->delivery->lat  : null,
            'lng' => $this->delivery ? $this->delivery->lng  : null,

        ];
    }
}
