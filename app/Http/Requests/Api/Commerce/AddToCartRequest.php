<?php

namespace App\Http\Requests\Api\Commerce;

use App\Http\Requests\Api\ApiFormRequest;

class AddToCartRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('quantity')) {
            $this->merge(['quantity' => 1]);
        }
    }

    public function rules(): array
    {
        return [
            'product_slug' => ['required', 'string', 'exists:products,slug'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
