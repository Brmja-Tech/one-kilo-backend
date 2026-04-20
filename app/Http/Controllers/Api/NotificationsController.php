<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationsResource;
use App\Http\Resources\OrderResource;
use App\Services\Api\Commerce\AddressService;
use App\Services\Api\Commerce\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationsController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function notifications(Request $request)
    {

        $notifications = $this->service->getNotifications();

        return ApiResponse::sendResponse(
            200,
            __('front.notifications-successfully'),
            NotificationsResource::collection($notifications),
            [
                'total' => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
            ]
        );
    }

}
