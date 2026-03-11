<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $galleryMap = [
            'mango-juice-1l' => [
                ['image' => 'dashboard/app-assets/images/slider/04.jpg', 'sort_order' => 1],
                ['image' => 'dashboard/app-assets/images/slider/05.jpg', 'sort_order' => 2],
            ],
            'sea-salt-chips' => [
                ['image' => 'dashboard/app-assets/images/slider/06.jpg', 'sort_order' => 1],
                ['image' => 'dashboard/app-assets/images/slider/07.jpg', 'sort_order' => 2],
            ],
            'full-cream-milk-1l' => [
                ['image' => 'dashboard/app-assets/images/slider/08.jpg', 'sort_order' => 1],
            ],
            'laundry-detergent-25l' => [
                ['image' => 'dashboard/app-assets/images/slider/09.jpg', 'sort_order' => 1],
                ['image' => 'dashboard/app-assets/images/slider/10.jpg', 'sort_order' => 2],
            ],
        ];

        foreach ($galleryMap as $productSlug => $images) {
            $product = Product::query()->where('slug', $productSlug)->first();

            if (! $product) {
                continue;
            }

            $sortOrders = collect($images)->pluck('sort_order')->all();

            foreach ($images as $imageData) {
                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'sort_order' => $imageData['sort_order'],
                    ],
                    [
                        'product_id' => $product->id,
                        'image' => $imageData['image'],
                    ]
                );
            }

            ProductImage::query()
                ->where('product_id', $product->id)
                ->whereNotIn('sort_order', $sortOrders)
                ->delete();
        }
    }
}
