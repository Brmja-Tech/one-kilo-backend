<?php

namespace App\Http\Requests\Api\Commerce;

use App\Http\Requests\Api\ApiFormRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class CheckoutRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'address_id' => [
                'required',
                'integer',
                Rule::exists('addresses', 'id')->where(fn ($query) => $query
                    ->where('user_id', $this->user('sanctum')?->id)
                    ->where('status', true)),
            ],
            'payment_method' => ['required', 'string', Rule::in(Order::paymentMethods())],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
