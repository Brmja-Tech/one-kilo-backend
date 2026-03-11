<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'shopper@onekilo.test')->first();

        if (! $user) {
            return;
        }

        $couponId = Coupon::query()->where('code', 'WELCOME10')->value('id');

        $cart = Cart::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['coupon_id' => $couponId]
        );

        $items = [
            'mango-juice-1l' => 2,
            'butter-croissant' => 1,
            'full-cream-milk-1l' => 1,
        ];

        $productIds = Product::query()
            ->whereIn('slug', array_keys($items))
            ->pluck('id', 'slug');

        foreach ($items as $slug => $quantity) {
            $productId = $productIds[$slug] ?? null;

            if (! $productId) {
                continue;
            }

            CartItem::query()->updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                ],
                [
                    'quantity' => $quantity,
                ]
            );
        }

        CartItem::query()
            ->where('cart_id', $cart->id)
            ->whereNotIn('product_id', $productIds->values())
            ->delete();
    }
}
