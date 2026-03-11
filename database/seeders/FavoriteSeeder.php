<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'shopper@onekilo.test')->first();

        if (! $user) {
            return;
        }

        $productSlugs = [
            'mango-juice-1l',
            'greek-yogurt',
            'imported-avocado',
        ];

        $productIds = Product::query()
            ->whereIn('slug', $productSlugs)
            ->pluck('id')
            ->all();

        foreach ($productIds as $productId) {
            Favorite::query()->firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
        }

        Favorite::query()
            ->where('user_id', $user->id)
            ->whereNotIn('product_id', $productIds)
            ->delete();
    }
}
