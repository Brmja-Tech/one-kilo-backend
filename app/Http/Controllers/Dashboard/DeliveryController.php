<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DeliveryService;
use App\Services\Dashboard\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function userProfile($id,Request $request)
    {

        $fromDate = $request->filled('from_date')
            ? $request->from_date . ' 00:00:00'
            : now()->startOfDay()->toDateTimeString();

        $profileData = $this->userService->getProfileData((int) $id,$fromDate);

        if (! $profileData) {
            return redirect()->route('dashboard.deliveries.index')->with('error', __('dashboard.user-not-found'));
        }

        if ($request->ajax()) {
            return view('dashboard.partials.delivery-report',
                $profileData)->render();
        }

        return view('dashboard.deliveries.profile', $profileData);
    }



}
