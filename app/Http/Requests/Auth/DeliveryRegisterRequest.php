<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;

class DeliveryRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $phone = $this->input('phone');
            if (preg_match('/^\+200/', $phone)) {
                $phone = preg_replace('/^\+200/', '+20', $phone, 1);
            }
            $this->merge([
                'phone' => $phone,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name'              => 'required|string|max:255',
            'email'             => 'nullable|string|email|max:255|unique:deliveries,email',
            'phone'             => [
                'required',
                'string',
                'regex:/^(\+20|0)\d{9,10}$/',
                'unique:deliveries,phone',
            ],
            'password'          => 'required|string|min:8|confirmed',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'national_id_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'license_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'vehicle_license_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'vehicle_type'             => 'nullable|string',

        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name'     => __('dashboard.name'),
            'phone'    => __('dashboard.phone'),
            'email'    => __('dashboard.email'),
            'password' => __('dashboard.password'),
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
