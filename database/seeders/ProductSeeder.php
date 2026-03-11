<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category' => 'Juice',
                'name' => 'Mango Juice 1L',
                'short_description' => 'Chilled mango juice with no artificial colors.',
                'description' => 'A family-size mango juice bottle made for breakfast tables and quick refreshment.',
                'image' => 'dashboard/app-assets/images/slider/03.jpg',
                'price' => 42,
                'discount_type' => 'amount',
                'discount_value' => 5,
                'discount_starts_at' => now()->subDays(2),
                'discount_ends_at' => now()->addDays(5),
                'sku' => 'JUICE-MANGO-1L',
                'stock' => 40,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'category' => 'Soda',
                'name' => 'Cola Can 330ml',
                'short_description' => 'Classic sparkling cola can.',
                'description' => 'Single chilled cola can for grab-and-go orders.',
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'price' => 18,
                'sku' => 'SODA-COLA-330',
                'stock' => 0,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Bakery',
                'name' => 'Butter Croissant',
                'short_description' => 'Freshly baked croissant with a buttery finish.',
                'description' => 'Soft layers and a light crisp shell, ideal for breakfast or coffee breaks.',
                'image' => 'dashboard/app-assets/images/slider/05.jpg',
                'price' => 12,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_starts_at' => now()->subDays(10),
                'discount_ends_at' => now()->subDays(5),
                'sku' => 'BAK-CROISSANT',
                'stock' => 25,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Snacks',
                'name' => 'Sea Salt Chips',
                'short_description' => 'Crunchy potato chips with a light sea salt finish.',
                'description' => 'A pantry staple snack with a clean salty flavor and crisp texture.',
                'image' => 'dashboard/app-assets/images/slider/06.jpg',
                'price' => 20,
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'discount_starts_at' => now()->subDay(),
                'discount_ends_at' => now()->addDays(7),
                'sku' => 'SNACK-CHIPS-SS',
                'stock' => 60,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'category' => 'Dairy',
                'name' => 'Full Cream Milk 1L',
                'short_description' => 'Fresh full cream milk for daily use.',
                'description' => 'Suitable for coffee, cereals, baking, and everyday household use.',
                'image' => 'dashboard/app-assets/images/slider/07.jpg',
                'price' => 34,
                'sku' => 'DAIRY-MILK-1L',
                'stock' => 80,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Dairy',
                'name' => 'Greek Yogurt',
                'short_description' => 'Rich and creamy yogurt cup.',
                'description' => 'High-protein yogurt with a thick texture for breakfast bowls and snacks.',
                'image' => 'dashboard/app-assets/images/slider/08.jpg',
                'price' => 28,
                'discount_type' => 'amount',
                'discount_value' => 3,
                'discount_starts_at' => now()->addDay(),
                'discount_ends_at' => now()->addDays(7),
                'sku' => 'DAIRY-YOGURT-GR',
                'stock' => 30,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Citrus',
                'name' => 'Fresh Lemons 1kg',
                'short_description' => 'Bright and juicy lemons sold by the kilo.',
                'description' => 'Ideal for juices, marinades, tea, and everyday kitchen prep.',
                'image' => 'dashboard/app-assets/images/slider/09.jpg',
                'price' => 15,
                'sku' => 'FRUIT-LEMON-1KG',
                'stock' => 100,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Imported Fruits',
                'name' => 'Imported Avocado',
                'short_description' => 'Premium imported avocados.',
                'description' => 'Creamy imported avocados suitable for salads, sandwiches, and healthy bowls.',
                'image' => 'dashboard/app-assets/images/slider/10.jpg',
                'price' => 49,
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'discount_starts_at' => now()->subDays(3),
                'discount_ends_at' => now()->addDays(4),
                'sku' => 'FRUIT-AVOCADO-IMP',
                'stock' => 18,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'category' => 'Leafy Greens',
                'name' => 'Baby Spinach',
                'short_description' => 'Fresh baby spinach leaves.',
                'description' => 'Tender leafy greens ready for salads, sauteing, or smoothie prep.',
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'price' => 22,
                'sku' => 'VEG-SPINACH-BABY',
                'stock' => 35,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Cleaning Supplies',
                'name' => 'Laundry Detergent 2.5L',
                'short_description' => 'Concentrated detergent for everyday loads.',
                'description' => 'Large-format detergent for colored and mixed fabrics with fresh fragrance.',
                'image' => 'dashboard/app-assets/images/slider/02.jpg',
                'price' => 95,
                'discount_type' => 'amount',
                'discount_value' => 10,
                'discount_starts_at' => now()->subDays(1),
                'discount_ends_at' => now()->addDays(10),
                'sku' => 'CLEAN-DETERGENT-25L',
                'stock' => 12,
                'is_featured' => true,
                'status' => true,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::query()->where('name', $productData['category'])->firstOrFail();

            $product = Product::query()->firstOrNew([
                'name' => $productData['name'],
            ]);

            unset($productData['category']);

            $product->fill([
                ...$productData,
                'category_id' => $category->id,
            ]);
            $product->save();
        }
    }
}
