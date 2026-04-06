<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Commerce\CategoryIndexRequest;
use App\Http\Resources\CategoryResource;
use App\Services\Api\Commerce\CategoryService;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service) {}

    public function index(CategoryIndexRequest $request)
    {
        $categories = $this->service->paginate($request->filters());

        Log::info('request', $request->all());
        Log::info('categories', [
            'items' => $categories->items(),
            'pagination' => [
                'total' => $categories->total(),
                'count' => $categories->count(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
            ],
        ]);
        return ApiResponse::sendResponse(
            200,
            __('front.categories-retrieved-successfully'),
            CategoryResource::collection($categories),
            [
                'total' => $categories->total(),
                'count' => $categories->count(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
            ]
        );
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
