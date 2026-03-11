<?php

namespace App\Repositories\Api\Auth;

use App\Models\User;
use Fisal\Otp\Otp;

class ForgotRepository
{
    protected $otp;

    public function __construct()
    {
        $this->otp = new Otp();
    }




    public function getUserByPhone($phone)
    {
        return User::where('phone', $phone)->first();
    }





    public function verifyOtp($data)
    {
        return $this->otp->validate($data['phone'], $data['token']);
    }




    public function resetPassword($data)
    {
        $user = $this->getUserByPhone($data['phone']);
        if (!$user) return false;
        $user->password = bcrypt($data['password']);
        $user->save();
        return $user;
    }
}
