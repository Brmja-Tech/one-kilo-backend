<?php

namespace App\Http\Requests\Api\Commerce;

use App\Http\Requests\Api\ApiFormRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends ApiFormRequest
{


    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Order::statuses())],
        ];
    }
}
