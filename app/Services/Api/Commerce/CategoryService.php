<?php

namespace App\Services\Api\Commerce;

use App\Models\Category;
use App\Repositories\Api\Commerce\CategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function __construct(protected CategoryRepository $repository)
    {
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForIndex($filters);
    }

    public function show(string $slug): Category
    {
        return $this->repository->findActiveBySlug($slug);
    }
}
