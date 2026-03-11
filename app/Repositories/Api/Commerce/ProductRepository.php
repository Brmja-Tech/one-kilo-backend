<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository
{
    public function paginateForIndex(array $filters, ?int $userId = null): LengthAwarePaginator
    {
        $query = Product::query()
            ->select('products.*')
            ->with(['category', 'images']);

        $this->attachFavoriteState($query, $userId);
        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort'] ?? 'latest');

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    public function findActiveBySlug(string $slug, ?int $userId = null): Product
    {
        $query = Product::query()
            ->select('products.*')
            ->active()
            ->with(['category', 'images'])
            ->where('slug', $slug);

        $this->attachFavoriteState($query, $userId);

        return $query->firstOrFail();
    }

    public function findActiveBySlugForCart(string $slug): Product
    {
        return Product::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    private function attachFavoriteState(Builder $query, ?int $userId): void
    {
        if ($userId) {
            $query->withExists([
                'favorites as is_favorite' => fn (Builder $favoriteQuery) => $favoriteQuery->where('user_id', $userId),
            ]);

            return;
        }

        $query->selectRaw('0 as is_favorite');
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids']);
        } elseif (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $query->where(function (Builder $searchQuery) use ($filters) {
                $searchQuery
                    ->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('short_description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (array_key_exists('is_featured', $filters)) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (array_key_exists('in_stock', $filters)) {
            $filters['in_stock']
                ? $query->where('stock', '>', 0)
                : $query->where('stock', '<=', 0);
        }

        if (array_key_exists('has_discount', $filters)) {
            [$discountSql, $discountBindings] = $this->activeDiscountSql();

            $filters['has_discount']
                ? $query->whereRaw($discountSql, $discountBindings)
                : $query->whereRaw('NOT (' . $discountSql . ')', $discountBindings);
        }

        if (array_key_exists('min_price', $filters)) {
            [$priceSql, $bindings] = $this->effectivePriceSql();
            $query->whereRaw('(' . $priceSql . ') >= ?', [...$bindings, $filters['min_price']]);
        }

        if (array_key_exists('max_price', $filters)) {
            [$priceSql, $bindings] = $this->effectivePriceSql();
            $query->whereRaw('(' . $priceSql . ') <= ?', [...$bindings, $filters['max_price']]);
        }
    }

    private function applySorting(Builder $query, string $sort): void
    {
        if (in_array($sort, ['price_asc', 'price_desc'], true)) {
            [$priceSql, $bindings] = $this->effectivePriceSql();

            $query->orderByRaw(
                $priceSql . ' ' . ($sort === 'price_asc' ? 'asc' : 'desc'),
                $bindings
            );

            return;
        }

        match ($sort) {
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };
    }

    private function activeDiscountSql(): array
    {
        $now = now()->toDateTimeString();

        return [
            "discount_type IS NOT NULL
            AND discount_value IS NOT NULL
            AND discount_value > 0
            AND (discount_starts_at IS NULL OR discount_starts_at <= ?)
            AND (discount_ends_at IS NULL OR discount_ends_at >= ?)",
            [$now, $now],
        ];
    }

    private function effectivePriceSql(): array
    {
        [$activeDiscountSql, $bindings] = $this->activeDiscountSql();

        $sql = "CASE
            WHEN ({$activeDiscountSql}) AND discount_type = 'amount'
                THEN CASE WHEN price - discount_value < 0 THEN 0 ELSE price - discount_value END
            WHEN ({$activeDiscountSql}) AND discount_type = 'percentage'
                THEN CASE WHEN price - (price * discount_value / 100) < 0 THEN 0 ELSE price - (price * discount_value / 100) END
            ELSE price
        END";

        return [$sql, [...$bindings, ...$bindings]];
    }
}
