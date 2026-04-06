<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Requests\Api\Commerce\CategoryIndexRequest;
use App\Http\Resources\CategoryResource;
use App\Services\Api\Commerce\CategoryService;
use Illuminate\Support\Facades\Log;

class CategoryController extends ApiController
{
    public function __construct(protected CategoryService $service) {}

    public function index(CategoryIndexRequest $request)
    {
        $categories = $this->service->paginate($request->filters());

        Log::info('categories', $categories);
        return response()->json($categories);
        // return ApiResponse::sendResponse(
        //     200,
        //     __('front.categories-retrieved-successfully'),
        //     CategoryResource::collection($categories),
        //     $this->paginationData($categories)
        // );
    }

    public function show(string $slug)
    {
        $category = $this->service->show($slug);

        return ApiResponse::sendResponse(
            200,
            __('front.category-retrieved-successfully'),
            new CategoryResource($category)
        );
    }
}
