<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;

class LoginRequest extends FormRequest
{
    /**
     * السماح بإرسال الطلب
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * تهيئة البيانات قبل الفالديشن:
     * - إذا جاء رقم الهاتف بـ +200... نحولها إلى +20...
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $phone = $this->input('phone');

            // لو بدأ بـ +200 (مثال: +2001140158807) نحذف صفر واحد
            if (preg_match('/^\+200/', $phone)) {
                $phone = preg_replace('/^\+200/', '+20', $phone, 1);
            }

            $this->merge([
                'phone' => $phone,
            ]);
        }
    }

    /**
     * قواعد الفالديشن
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone'     => 'required|string|exists:users,phone',
            'password'  => 'required|string',
            'fcm_token' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone'    => __('validation.phone'),
            'password' => __('validation.password'),
        ];
    }

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
