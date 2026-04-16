<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;

class DeliveryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name'              => 'nullable|string|max:255',
            'email'             => 'nullable|string|email|max:255|unique:deliveries,email',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'full_name'     => __('dashboard.name'),
            'email'    => __('dashboard.email'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(
                    422,
                    $validator->errors()->first(),
                    $validator->errors()
                )
            );
        }

        parent::failedValidation($validator);
    }
}
