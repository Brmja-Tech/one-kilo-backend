<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function paginateForIndex(array $filters): LengthAwarePaginator
    {
        $query = Category::query();

        if ($filters['include_children'] ?? false) {
            $query->with('childrenRecursive');
        }

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort'] ?? 'sort_order');

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    public function findActiveBySlug(string $slug): Category
    {
        return Category::query()
            ->active()
            ->with(['parent', 'childrenRecursive'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function findActiveById(int $id): Category
    {
        return Category::query()
            ->active()
            ->whereKey($id)
            ->firstOrFail();
    }

    public function branchIds(Category $category): array
    {
        $allCategories = Category::query()
            ->select(['id', 'parent_id'])
            ->get();

        $childrenByParent = $allCategories->groupBy(
            fn (Category $item) => $item->parent_id ?? 0
        );

        $ids = [$category->id];
        $queue = [$category->id];

        while ($queue !== []) {
            $parentId = array_shift($queue);

            /** @var Collection<int, Category> $children */
            $children = $childrenByParent->get($parentId, collect());

            foreach ($children as $child) {
                $ids[] = $child->id;
                $queue[] = $child->id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (array_key_exists('parent_id', $filters)) {
            $query->where('parent_id', $filters['parent_id']);

            return;
        }

        if (($filters['root_only'] ?? false) === true) {
            $query->whereNull('parent_id');
        }
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            'name' => $query->orderBy('name'),
            default => $query
                ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sort_order')
                ->orderBy('name'),
        };
    }
}
