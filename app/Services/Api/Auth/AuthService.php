<?php

namespace App\Services\Api\Auth;

use App\Utils\ImageManger;
use App\Notifications\SendOtpNotify;
use App\Repositories\Api\Auth\AuthRepository;

class AuthService
{
    protected $authRepository, $imageManager;


    public function __construct(AuthRepository $authRepository, ImageManger $imageManager)
    {
        $this->imageManager = $imageManager;
        $this->authRepository = $authRepository;
    } //End constructor Method





    public function register($credentials)
    {
        if (isset($credentials['image'])) {
            $credentials['image'] = $this->imageManager->uploadImage('/uploads/users/', $credentials['image']);
        } else {
            $credentials['image'] = null;
        }
        $user = $this->authRepository->register($credentials);
        if (!$user) {
            return false;
        }
        // Send OTP notification after registration
        $user->notify(new SendOtpNotify($user->phone));

        $user->tokens()->delete(); // Delete old tokens
        return $user ? [
            'phone' => $user->phone,
        ] : false;
    } //End register Method




    public function verifyOtp($data)
    {
        return $this->authRepository->verifyOtp($data);
    } //End verifyOtp Method


    public function login($credenshais, $guard, $remember = false)
    {
        return $this->authRepository->login($credenshais, $guard, $remember);
    } //End login Method



    public function resendOtp($data)
    {
        return $this->authRepository->resendOtp($data);
    } //End resendOtp Method



    public function logout($guard = null)
    {
        return $this->authRepository->logout($guard);
    } //End logout Method
}
