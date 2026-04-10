<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name'              => 'required|string|max:255',
            'email'             => 'nullable|string|email|max:255|unique:users,email',
            'phone'             => [
                'required',
                'string',
                'regex:/^(\+20|0)\d{9,10}$/',
                'unique:users,phone',
            ],
            'birth_date'        => 'nullable|date_format:Y-m-d',
            'password'          => 'required|string|min:8|confirmed',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'country_id'        => [
                'nullable',
                'integer',
                'required_with:governorate_id,region_id',
                Rule::exists('countries', 'id')->where(fn ($query) => $query->where('status', true)),
            ],
            'governorate_id'    => [
                'nullable',
                'integer',
                'required_with:region_id',
                Rule::exists('governorates', 'id')->where(function ($query) {
                    $query->where('status', true);

                    if ($this->filled('country_id')) {
                        $query->where('country_id', $this->integer('country_id'));
                    }
                }),
            ],
            'region_id' => [
                'nullable',
                'integer',
                Rule::exists('regions', 'id')->where(function ($query) {
                    $query->where('status', true);

                    if ($this->filled('governorate_id')) {
                        $query->where('governorate_id', $this->integer('governorate_id'));
                    }
                }),
            ],

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
