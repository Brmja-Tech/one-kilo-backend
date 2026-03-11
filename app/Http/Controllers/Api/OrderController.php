<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Requests\Api\Commerce\OrderIndexRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderResource;
use App\Services\Api\Commerce\OrderService;

class OrderController extends ApiController
{
    public function __construct(protected OrderService $service)
    {
    }

    public function index(OrderIndexRequest $request)
    {
        $orders = $this->service->paginate(
            auth('sanctum')->user()->id,
            $request->filters()
        );

        return ApiResponse::sendResponse(
            200,
            __('front.orders-retrieved-successfully'),
            OrderResource::collection($orders),
            $this->paginationData($orders)
        );
    }

    public function show(string $reference)
    {
        $order = $this->service->show(auth('sanctum')->user()->id, $reference);

        return ApiResponse::sendResponse(
            200,
            __('front.order-retrieved-successfully'),
            new OrderDetailsResource($order)
        );
    }
}
