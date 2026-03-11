<?php

namespace App\Services\Api\Commerce;

use App\Repositories\Api\Commerce\FavoriteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteService
{
    public function __construct(
        protected FavoriteRepository $favoriteRepository,
        protected ProductService $productService
    ) {
    }

    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->favoriteRepository->paginateProducts($userId, $perPage);
    }

    public function toggle(int $userId, string $productSlug): array
    {
        $product = $this->productService->findActiveBySlugForCart($productSlug);
        $isFavorite = $this->favoriteRepository->toggle($userId, $product->id);
        $displayProduct = $this->productService->show($productSlug, $userId);

        return [
            'is_favorite' => $isFavorite,
            'product' => $displayProduct,
        ];
    }
}
