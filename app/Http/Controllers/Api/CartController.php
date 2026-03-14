<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Requests\Api\Commerce\AddToCartRequest;
use App\Http\Requests\Api\Commerce\ApplyCouponRequest;
use App\Http\Requests\Api\Commerce\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\Api\Commerce\CartService;
use Illuminate\Http\Request;

class CartController extends ApiController
{
    public function __construct(protected CartService $service) {}

    public function show(Request $request)
    {
        $cart = $this->service->current(auth('sanctum')->user()->id);

        return ApiResponse::sendResponse(
            200,
            __('front.cart-retrieved-successfully'),
            new CartResource($cart)
        );
    }

    public function add(AddToCartRequest $request)
    {
        $cart = $this->service->addItem(
            auth('sanctum')->user()->id,
            $request->validated('product_slug'),
            (int) $request->validated('quantity')
        );

        return ApiResponse::sendResponse(
            200,
            __('front.product-add-to-cart'),
            []
            //new CartResource($cart)
        );
    }

    public function updateItem(UpdateCartItemRequest $request, int $id)
    {
        $cart = $this->service->updateItem(
            auth('sanctum')->user()->id,
            $id,
            (int) $request->validated('quantity')
        );

        return ApiResponse::sendResponse(
            200,
            __('front.cart-item-updated-successfully'),
            new CartResource($cart)
        );
    }

    public function removeItem(Request $request, int $id)
    {
        $cart = $this->service->removeItem(auth('sanctum')->user()->id, $id);

        return ApiResponse::sendResponse(
            200,
            __('front.product-removed-from-cart'),
            new CartResource($cart)
        );
    }

    public function clear(Request $request)
    {
        $cart = $this->service->clear(auth('sanctum')->user()->id);

        return ApiResponse::sendResponse(
            200,
            __('front.cart-cleared-successfully'),
            new CartResource($cart)
        );
    }

    public function applyCoupon(ApplyCouponRequest $request)
    {
        $cart = $this->service->applyCoupon(
            auth('sanctum')->user()->id,
            $request->validated('code')
        );

        return ApiResponse::sendResponse(
            200,
            __('front.coupon-applied-successfully'),
            new CartResource($cart)
        );
    }

    public function removeCoupon(Request $request)
    {
        $cart = $this->service->removeCoupon(auth('sanctum')->user()->id);

        return ApiResponse::sendResponse(
            200,
            __('front.coupon-removed-successfully'),
            new CartResource($cart)
        );
    }
}
