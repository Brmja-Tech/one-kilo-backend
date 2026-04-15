<?php

namespace App\Repositories\Api\Auth;

use App\Http\Resources\DeliveryResource;

use App\Models\Delivery;
use App\Models\User;
use App\Notifications\SendOtpNotify;
use Fisal\Otp\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeliveryAuthRepository
{
    protected $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    } // End constructor

    public function register($credentials)
    {

        $delivery = Delivery::create([
            'full_name'              => $credentials['full_name'],
            'email'             => $credentials['email'] ?? null,
            'phone'             => $credentials['phone'],
            'password'          => Hash::make($credentials['password']),
            'status'            => "pending",
            'image'             => $credentials['image'] ?? null,
            'vehicle_type'     => $credentials['vehicle_type'] ?? null,
            'national_id_image'        => $credentials['national_id_image'] ?? null,
            'license_image'    => $credentials['license_image'] ?? null,
            'vehicle_license_image'         => $credentials['vehicle_license_image'] ?? null,
        ]);
        return $delivery;
    } // End method register



    public function verifyOtp($data)
    {
        $delivery = Delivery::where('phone', $data['phone'])->first();

        if (!$delivery) {
            return [
                'status' => 422,
                'message' => __('front.user-not-found'),
                'data' => []
            ];
        }

        if (! empty($data['fcm_token'])) {
            $delivery->update(['fcm_token' => $data['fcm_token']]);
        }

        $otp = $this->otp->validate($data['phone'], $data['token']);
        if (!$otp->status) {
            return [
                'status' => 422,
                'message' => __('front.invalid-otp'),
                'data' => []
            ];
        }

        $delivery->update([
            'status' => 1,
            'email_verified_at' => now()
        ]);

        $delivery->currentAccessToken()?->delete();
        $token = $delivery->createToken('auth_token')->plainTextToken;

        return [
            'status' => 200,
            'message' => __('front.otp-verified'),
            'data' => [
                'user' => DeliveryResource::make($delivery),
                'token' => $token,
            ]
        ];
    } // End verifyOtp Method


    public function login($credentials, $guard, $remember = false)
    {
        if (auth('sanctum')->check()) {
            return [
                'status' => 403,
                'message' => __('front.already-logged-in'),
                'data' => []
            ];
        }

        $delivery = Delivery::where('phone', $credentials['phone'])->first();

        if (!$delivery) {
            return [
                'status' => 422,
                'message' => __('front.user-not-found'),
                'data' => []
            ];
        }
        if ($delivery->email_verified_at == null) {
            $delivery->notify(new SendOtpNotify($delivery->phone));
            return [
                'status' => 415,
                'message' => __('front.verify-account-first'),
                'data' => ['phone' => $delivery->phone]
            ];
        }
        if ($delivery->status ==  Delivery::STATUS_PENDING || $delivery->status ==  Delivery::STATUS_REJECTED) {
            return [
                'status' => 422,
                'message' => __('front.account-not-activated'),
                'data' => ['phone' => $delivery->phone]
            ];
        }

        if ($delivery->login_status == 0) {
            return [
                'status' => 422,
                'message' => __('front.account-not-activated'),
                'data' => ['phone' => $delivery->phone]
            ];
        }

        if (!Hash::check($credentials['password'], $delivery->password)) {
            return [
                'status' => 422,
                'message' => __('front.invalid-credentials'),
                'data' => []
            ];
        }
        $delivery->tokens()->delete();
        $token = $delivery->createToken('auth_token')->plainTextToken;
        if (! empty($credentials['fcm_token'])) {
            $delivery->update(['fcm_token' => $credentials['fcm_token']]);
        }

        return [
            'status' => 200,
            'message' => __('front.login-success'),
            'data' => [
                'user' => DeliveryResource::make($delivery),
                'token' => $token
            ]
        ];
    } // End login Method



    public function resendOtp($data)
    {
        $delivery = Delivery::where('phone', $data['phone'])->first();
        if (!$delivery) {
            return [
                'status' => 422,
                'message' => __('front.user-not-found'),
                'data' => []
            ];
        }

        // Send new OTP
        $delivery->notify(new SendOtpNotify($delivery->phone));

        return [
            'status' => 200,
            'message' => __('front.otp-resent-successfully'),
            'data' => [
                'phone' => $delivery->phone
            ]
        ];
    } // End resendOtp Method



    public function logout($guard = null)
    {
        $delivery = $guard ? Auth::guard($guard)->user() : Auth::user();

        if ($delivery) {
            $delivery->currentAccessToken()?->delete();
            return [
                'status' => 200,
                'message' => __('front.logout-success'),
                'data' => []
            ];
        }

        return [
            'status' => 422,
            'message' => __('front.logout-failed'),
            'data' => []
        ];
    } // End logout Method

}
