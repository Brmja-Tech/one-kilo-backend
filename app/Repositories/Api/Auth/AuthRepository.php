<?php

namespace App\Repositories\Api\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\SendOtpNotify;
use Fisal\Otp\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    protected $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    } // End constructor

    public function register($credentials)
    {

        $user = User::create([
            'name'              => $credentials['name'],
            'email'             => $credentials['email'] ?? null,
            'phone'             => $credentials['phone'],
            'birth_date'        => $credentials['birth_date'],
            'password'          => Hash::make($credentials['password']),
            'status'            => 0,
            'image'             => $credentials['image'] ?? null,
            'country_id'        => $credentials['country_id'] ?? null,
            'governorate_id'    => $credentials['governorate_id'] ?? null,
            'region_id'         => $credentials['region_id'] ?? null,
        ]);
        return $user;
    } // End method register



    public function verifyOtp($data)
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            return [
                'status' => 422,
                'message' => __('front.user-not-found'),
                'data' => []
            ];
        }

        if (! empty($data['fcm_token'])) {
            $user->update(['fcm_token' => $data['fcm_token']]);
        }

        $otp = $this->otp->validate($data['phone'], $data['token']);
        if (!$otp->status) {
            return [
                'status' => 422,
                'message' => __('front.invalid-otp'),
                'data' => []
            ];
        }

        $user->update([
            'status' => 1,
            'email_verified_at' => now()
        ]);

        $user->currentAccessToken()?->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status' => 200,
            'message' => __('front.otp-verified'),
            'data' => [
                'user' => UserResource::make($user),
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

        $user = User::where('phone', $credentials['phone'])->first();

        if (!$user) {
            return [
                'status' => 422,
                'message' => __('front.user-not-found'),
                'data' => []
            ];
        }

        if ($user->email_verified_at == null) {
            $user->notify(new SendOtpNotify($user->phone));
            return [
                'status' => 415,
                'message' => __('front.verify-account-first'),
                'data' => ['phone' => $user->phone]
            ];
        }
        if ($user->status == 0) {
            return [
                'status' => 422,
                'message' => __('front.account-not-activated'),
                'data' => ['phone' => $user->phone]
            ];
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return [
                'status' => 422,
                'message' => __('front.invalid-credentials'),
                'data' => []
            ];
        }
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        if (! empty($credentials['fcm_token'])) {
            $user->update(['fcm_token' => $credentials['fcm_token']]);
        }

        return [
            'status' => 200,
            'message' => __('front.login-success'),
            'data' => [
                'user' => UserResource::make($user),
                'token' => $token
            ]
        ];
    } // End login Method



    public function resendOtp($data)
    {
        $user = User::where('phone', $data['phone'])->first();
        if (!$user) {
            return [
                'status' => 422,
                'message' => __('front.user-not-found'),
                'data' => []
            ];
        }

        // Send new OTP
        $user->notify(new SendOtpNotify($user->phone));

        return [
            'status' => 200,
            'message' => __('front.otp-resent-successfully'),
            'data' => [
                'phone' => $user->phone
            ]
        ];
    } // End resendOtp Method



    public function logout($guard = null)
    {
        $user = $guard ? Auth::guard($guard)->user() : Auth::user();

        if ($user) {
            $user->currentAccessToken()?->delete();
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
