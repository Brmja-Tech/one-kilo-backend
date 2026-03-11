<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Requests\Api\Commerce\ToggleFavoriteRequest;
use App\Http\Resources\ProductResource;
use App\Services\Api\Commerce\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends ApiController
{
    public function __construct(protected FavoriteService $service) {}

    public function index(Request $request)
    {
        $favorites = $this->service->paginate(
            auth('sanctum')->user()->id,
            (int) $request->integer('per_page', 15)
        );

        return ApiResponse::sendResponse(
            200,
            __('front.favorites-retrieved-successfully'),
            ProductResource::collection($favorites),
            $this->paginationData($favorites)
        );
    }

    public function toggle(ToggleFavoriteRequest $request)
    {
        $result = $this->service->toggle(
            auth('sanctum')->user()->id,
            $request->validated('product_slug')
        );

        return ApiResponse::sendResponse(
            200,
            $result['is_favorite']
                ? __('front.favorite-added-successfully')
                : __('front.favorite-removed-successfully'),
            [
                'is_favorite' => $result['is_favorite'],
                'product' => new ProductResource($result['product']),
            ]
        );
    }
}
