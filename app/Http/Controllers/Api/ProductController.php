<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Commerce\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductSummaryResource;
use App\Services\Api\Commerce\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index(ProductIndexRequest $request)
    {
        $products = $this->service->paginate(
            $request->filters(),
            $request->user('sanctum')?->id
        );

        return ApiResponse::sendResponse(
            200,
            __('front.products-retrieved-successfully'),
            ProductResource::collection($products),
            [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
            ]
        );
    }

    public function bestSelling(ProductIndexRequest $request)
    {
        $filters = $request->filters();

        $products = $this->service->getBestSellingProducts(
            ['per_page' => $filters['per_page']],
            $request->user('sanctum')?->id
        );

        return ApiResponse::sendResponse(
            200,
            __('front.products-retrieved-successfully'),
            ProductSummaryResource::collection($products),
            [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
            ]
        );
    }

    public function show(Request $request, string $slug)
    {
        $product = $this->service->show($slug, $request->user('sanctum')?->id);

        return ApiResponse::sendResponse(
            200,
            __('front.product-retrieved-successfully'),
            new ProductResource($product)
        );
    }

    public function categoryProducts(ProductIndexRequest $request, string $slug)
    {
        $products = $this->service->paginateForCategory(
            $slug,
            $request->filters(),
            $request->user('sanctum')?->id
        );

        return ApiResponse::sendResponse(
            200,
            __('front.products-retrieved-successfully'),
            ProductResource::collection($products),
            [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
            ]
        );
    }
}
