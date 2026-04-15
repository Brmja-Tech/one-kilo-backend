<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\Api\Auth\DeliveryForgotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Services\Api\Auth\ForgotService;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;

class DeliveryForgotController extends Controller
{
    protected $forgotService;

    public function __construct(DeliveryForgotService $forgotService)
    {
        $this->forgotService = $forgotService;
    }




    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|exists:deliveries,phone',
        ]);

        if (! $this->forgotService->sendOTP($data['phone'])) {
            return ApiResponse::sendResponse(
                404,
                __('front.something-filed'),
                []
            );
        }

        return ApiResponse::sendResponse(
            200,
            __('front.otp-sent'),
            []
        );
    }




    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|exists:deliveries,phone',
            'token' => 'required|string',
        ]);

        $isValid = $this->forgotService->verifyOtp($data);
        if (!$isValid) {
            return ApiResponse::sendResponse(422, __('front.invalid-otp'));
        }

        // Save verified status for phone for 5 minutes
        Cache::put('verified_otp_' . $data['phone'], true, now()->addMinutes(5));

        return ApiResponse::sendResponse(200, __('front.otp-verified'));
    }




    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'phone'    => 'required|exists:deliveries,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Cache::get('verified_otp_' . $data['phone'])) {
            return ApiResponse::sendResponse(403, __('front.otp-not-verified'), []);
        }

        $user = $this->forgotService->resetPassword($data);
        Cache::forget('verified_otp_' . $data['phone']);

        return ApiResponse::sendResponse(200, __('front.password-reset-successful'), UserResource::make($user));
    }




    public function resendOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|exists:deliveries,phone',
        ]);

        $this->forgotService->sendOTP($data['phone']);
        return ApiResponse::sendResponse(200, __('front.otp-resent-successfully'), []);
    }
}
