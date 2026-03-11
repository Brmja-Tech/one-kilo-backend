<?php

namespace App\Http\Requests\Api\Commerce;

use App\Http\Requests\Api\ApiFormRequest;

class ToggleFavoriteRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'product_slug' => ['required', 'string', 'exists:products,slug'],
        ];
    }
}
