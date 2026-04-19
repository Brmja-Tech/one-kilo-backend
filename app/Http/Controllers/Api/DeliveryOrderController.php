<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Commerce\OrderIndexRequest;
use App\Http\Requests\Api\Commerce\UpdateOrderStatusRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Api\Commerce\FirebaseService;
use App\Services\Api\Commerce\OrderService;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryOrderController extends Controller
{
    public function __construct(protected OrderService $service , protected FirebaseService $firebaseService) {}

    public function currentOrders(OrderIndexRequest $request)
    {
        $orders = $this->service->currentOrders(
            auth('sanctum')->user()->id,
            $request->filters()
        );

        return ApiResponse::sendResponse(
            200,
            __('front.orders-retrieved-successfully'),
            OrderResource::collection($orders),
            [
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
            ]
        );
    }


    public function pastOrders(OrderIndexRequest $request)
    {
        $orders = $this->service->pastOrders(
            auth('sanctum')->user()->id,
            $request->filters()
        );

        return ApiResponse::sendResponse(
            200,
            __('front.orders-retrieved-successfully'),
            OrderResource::collection($orders),
            [
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
            ]
        );
    }

    public function updateStatus(string $reference ,UpdateOrderStatusRequest $request)
    {
        $order = $this->service->updateStatus($reference,$request);


        $data = Order::STATUS_MESSAGES[$request->status] ?? null;

        $msg = $data['title']['ar'];
        $title = $data['message']['ar'];

        $order = Order::find($reference);
        $this->firebaseService->sendNotification($order->user->fcm_token??'',$title,$msg);
        $this->firebaseService->saveNotification($order->user,$order->id,$data['title'],$data['message']);

        return ApiResponse::sendResponse(
            200,
            __('front.order-status-updated-successfully'),
            new OrderDetailsResource($order)
        );
    }

    public function show(string $reference)
    {
        $order = $this->service->showForDelivery(auth('sanctum')->user()->id, $reference);

        return ApiResponse::sendResponse(
            200,
            __('front.order-retrieved-successfully'),
            new OrderDetailsResource($order)
        );
    }
}
