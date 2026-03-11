<?php

namespace App\Services\Api\Auth;

use App\Notifications\SendOtpNotify;
use App\Repositories\Api\Auth\ForgotRepository;

class ForgotService
{
    protected $forgotRepository;

    public function __construct(ForgotRepository $forgotRepository)
    {
        $this->forgotRepository = $forgotRepository;
    }




    public function sendOTP($phone)
    {
        $user = $this->forgotRepository->getUserByPhone($phone);
        if (!$user) return false;

        $user->notify(new SendOtpNotify($phone));
        return true;
    }




    public function verifyOtp($data)
    {
        $otp = $this->forgotRepository->verifyOtp($data);
        return $otp->status;
    }




    public function resetPassword($data)
    {
        return $this->forgotRepository->resetPassword($data);
    }
}
