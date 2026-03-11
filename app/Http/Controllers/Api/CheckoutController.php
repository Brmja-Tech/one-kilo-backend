<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Requests\Api\Commerce\CheckoutRequest;
use App\Http\Resources\CheckoutResource;
use App\Services\Api\Commerce\CheckoutService;

class CheckoutController extends ApiController
{
    public function __construct(protected CheckoutService $service)
    {
    }

    public function store(CheckoutRequest $request)
    {
        $checkout = $this->service->checkout(
            auth('sanctum')->user()->id,
            $request->validated()
        );

        return ApiResponse::sendResponse(
            200,
            __('front.checkout-completed-successfully'),
            new CheckoutResource($checkout)
        );
    }
}
