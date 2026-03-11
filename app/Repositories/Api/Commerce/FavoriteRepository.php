<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteRepository
{
    public function paginateProducts(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->select('products.*')
            ->active()
            ->whereHas('favorites', fn ($query) => $query->where('user_id', $userId))
            ->with(['category', 'images'])
            ->withExists([
                'favorites as is_favorite' => fn ($query) => $query->where('user_id', $userId),
            ])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function toggle(int $userId, int $productId): bool
    {
        $favorite = Favorite::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return false;
        }

        Favorite::query()->create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return true;
    }
}
