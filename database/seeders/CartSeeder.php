<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductSku;
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

        CartItem::query()->where('cart_id', $cart->id)->delete();

        $items = [
            ['product_slug' => 'mango-juice-1l', 'quantity' => 2],
            ['product_slug' => 'butter-croissant', 'quantity' => 1],
            ['product_slug' => 'full-cream-milk-1l', 'quantity' => 1],
            // Variant product example (ensures variant cart rules are exercised).
            ['product_slug' => 'tomatoes', 'quantity' => 1, 'sku_code' => 'TOM-500GM'],
        ];

        $productIds = Product::query()
            ->whereIn('slug', collect($items)->pluck('product_slug')->all())
            ->pluck('id', 'slug');

        foreach ($items as $item) {
            $productId = $productIds[$item['product_slug']] ?? null;

            if (! $productId) {
                continue;
            }

            $productSkuId = null;

            if (! empty($item['sku_code'])) {
                $sku = ProductSku::query()
                    ->where('sku', $item['sku_code'])
                    ->where('product_id', $productId)
                    ->first();

                if (! $sku) {
                    continue;
                }

                $productSkuId = $sku->id;
            }

            CartItem::query()->create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_sku_id' => $productSkuId,
                'quantity' => (int) $item['quantity'],
            ]);
        }
    }
}
