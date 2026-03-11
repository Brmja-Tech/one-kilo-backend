<?php

namespace App\Http\Requests\Api\Commerce;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateCartItemRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
