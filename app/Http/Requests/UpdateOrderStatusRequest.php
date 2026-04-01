<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('admin')?->hasAccess('orders_change_status');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Order::statuses())],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => __('dashboard.order-status'),
        ];
    }
}
