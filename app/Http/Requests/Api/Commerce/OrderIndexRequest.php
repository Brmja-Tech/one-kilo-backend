<?php

namespace App\Http\Requests\Api\Commerce;

use App\Http\Requests\Api\ApiFormRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(Order::statuses())],
            'payment_status' => ['nullable', 'string', Rule::in(Order::paymentStatuses())],
            'payment_method' => ['nullable', 'string', Rule::in(Order::paymentMethods())],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return [
            'status' => $this->validated('status'),
            'payment_status' => $this->validated('payment_status'),
            'payment_method' => $this->validated('payment_method'),
            'date_from' => $this->validated('date_from'),
            'date_to' => $this->validated('date_to'),
            'per_page' => (int) $this->validated('per_page', 15),
        ];
    }
}
