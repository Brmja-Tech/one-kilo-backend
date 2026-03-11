<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image ? asset($this->image) : '',
            'status' => (bool) $this->status,
            'parent_id' => $this->parent_id,
            'children' => $this->when(
                $this->relationLoaded('childrenRecursive'),
                fn () => CategoryResource::collection($this->childrenRecursive)
            ),
        ];
    }
}
