<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DeliveryLoginRequest;
use App\Http\Requests\Api\FirebaseLoginRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Auth\DeliveryRegisterRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Api\Auth\AuthService;
use App\Services\Api\Auth\DeliveryAuthService;
use App\Services\Api\Auth\FirebaseAuthService;
use Illuminate\Http\Request;

class DeliveryAuthController extends Controller
{
    protected $authService;

    public function __construct(DeliveryAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(DeliveryRegisterRequest $request)
    {
        $credentials = $request->only(['full_name', 'email', 'phone', 'password', 'birth_date', 'image', 'vehicle_type', 'national_id_image', 'vehicle_license_image','license_image']);
        $user = $this->authService->register($credentials);

        if (!$user) {
            return ApiResponse::sendResponse(422, __('front.user-registration-failed'), []);
        }

        return ApiResponse::sendResponse(201, __('front.user-registered-successfully'), $user);
    }




    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone'         => 'required|string|exists:deliveries,phone',
            'token'         => 'required|string',
            'fcm_token'     => 'nullable',
        ]);

        $response = $this->authService->verifyOtp($data);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function login(DeliveryLoginRequest $request)
    {
        $credenshais = $request->only('phone', 'password', 'fcm_token');
        $response = $this->authService->login($credenshais, 'web');
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function resendOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|exists:deliveries,phone',
        ]);
        $response = $this->authService->resendOtp($data);
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }




    public function logout(Request $request)
    {
        $response = $this->authService->logout();
        return ApiResponse::sendResponse($response['status'], $response['message'], $response['data']);
    }


    public function firebaseLogin(FirebaseLoginRequest $request, FirebaseAuthService $service)
    {
        return $service->loginWithFirebase(
            $request->input('id_token'),
            $request->input('device_name'),
            $request->input('fcm_token')
        );
    }
}
