<?php

namespace App\Services\Api\Commerce;

use App\Models\Product;
use App\Repositories\Api\Commerce\CategoryRepository;
use App\Repositories\Api\Commerce\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository
    ) {
    }

    public function paginate(array $filters, ?int $userId = null): LengthAwarePaginator
    {
        $filters = $this->resolveCategoryFilters($filters);

        return $this->productRepository->paginateForIndex($filters, $userId);
    }

    public function show(string $id, ?int $userId = null): Product
    {
        return $this->productRepository->findActiveById($id, $userId);
    }

    public function paginateForCategory($id, array $filters, ?int $userId = null): LengthAwarePaginator
    {
        $category = $this->categoryRepository->findActiveById($id);
        $filters['category_ids'] = $filters['include_descendants'] ?? true
            ? $this->categoryRepository->branchIds($category)
            : [$category->id];

        return $this->productRepository->paginateForIndex($filters, $userId);
    }

    public function findActiveBySlugForCart(string $slug): Product
    {
        return $this->productRepository->findActiveBySlugForCart($slug);
    }

    private function resolveCategoryFilters(array $filters): array
    {
        if (! empty($filters['category_slug'])) {
            $category = $this->categoryRepository->findActiveBySlug($filters['category_slug']);
            $filters['category_ids'] = $filters['include_descendants'] ?? true
                ? $this->categoryRepository->branchIds($category)
                : [$category->id];
        } elseif (! empty($filters['category_id'])) {
            $category = $this->categoryRepository->findActiveById((int) $filters['category_id']);
            $filters['category_ids'] = $filters['include_descendants'] ?? true
                ? $this->categoryRepository->branchIds($category)
                : [$category->id];
        }

        return $filters;
    }
}
