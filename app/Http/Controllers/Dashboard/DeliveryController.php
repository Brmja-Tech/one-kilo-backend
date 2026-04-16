<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DeliveryService;
use App\Services\Dashboard\UserService;

class DeliveryController extends Controller
{
    protected $userService;

    public function __construct(DeliveryService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view('dashboard.deliveries.index');
    }

    public function userProfile($id)
    {
        $profileData = $this->userService->getProfileData((int) $id);

        if (! $profileData) {
            return redirect()->route('dashboard.deliveries.index')->with('error', __('dashboard.user-not-found'));
        }

        return view('dashboard.deliveries.profile', $profileData);
    }
}
