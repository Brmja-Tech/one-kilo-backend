<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;

class FirebaseLoginRequest extends FormRequest
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
            'id_token'      => ['required', 'string'],
            'device_name'   => ['nullable', 'string', 'max:100'],
            'fcm_token'     => ['nullable', 'string'],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::sendResponse(
                422,
                $validator->errors()->first(),
                $validator->errors()
            )
        );
    }

    public function attributes(): array
    {
        return [
            'id_token'    => __('validation.attributes.id_token'),
            'device_name' => __('validation.attributes.device_name'),
            'fcm_token'   => __('validation.attributes.fcm_token'),
        ];
    }

}
