<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category' => 'Juice',
                'name' => [
                    'en' => 'Mango Juice 1L',
                    'ar' => 'عصير المانجو 1 لتر',
                ],
                'short_description' => [
                    'en' => 'Refreshing mango juice in a convenient 1-liter bottle.',
                    'ar' => 'عصير مانجو منعش في زجاجة سعة 1 لتر.',
                ],
                'description' => [
                    'en' => 'A family-size mango juice bottle made for breakfast tables and quick refreshment.',
                    'ar' => 'زجاجة عصير مانجو بحجم العائلة مصنوعة للكتاب والإنجاز السريع.',
                ],
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
                'name' => [
                    'en' => 'Cola Can 330ml',
                    'ar' => 'علبة كولا 330 مل',
                ],
                'short_description' => [
                    'en' => 'Classic sparkling cola can.',
                    'ar' => 'علبة كولا مغليه كلاسيكي.',
                ],
                'description' => [
                    'en' => 'Single chilled cola can for grab-and-go orders.',
                    'ar' => 'علبة كولا مبردة واحدة للطلبات السريعة.',
                ],
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'price' => 18,
                'sku' => 'SODA-COLA-330',
                'stock' => 0,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Bakery',
                'name' => [
                    'en' => 'Butter Croissant',
                    'ar' => 'كرواسون الزبدة',
                ],
                'short_description' => [
                    'en' => 'Freshly baked croissant with a buttery finish.',
                    'ar' => 'كرواسون مخبوز حديثاً مع إنهاء زبدي.',
                ],
                'description' => [
                    'en' => 'Soft layers and a light crisp shell, ideal for breakfast or coffee breaks.',
                    'ar' => 'طبقات ناعمة وقشرة مقرمشة خفيفة، مثالية للإفطار أو استراحة القهوة.',
                ],
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
                'name' => [
                    'en' => 'Sea Salt Chips',
                    'ar' => ' Chips الملح البحري',
                ],
                'short_description' => [
                    'en' => 'Crunchy potato chips with a light sea salt finish.',
                    'ar' => ' Chips البطاطا المقرمشة مع إنهاء ملح بحري خفيف.',
                ],
                'description' => [
                    'en' => 'A pantry staple snack with a clean salty flavor and crisp texture.',
                    'ar' => 'وجبة خفيفة أساسية في الخزانة مع طعم مالح نقي ونسيج مقرمش.',
                ],
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
                'name' => [
                    'en' => 'Full Cream Milk 1L',
                    'ar' => 'حليب كريم كامل 1 لتر',
                ],
                'short_description' => [
                    'en' => 'Fresh full cream milk for daily use.',
                    'ar' => 'حليب كريم كامل طازج للاستخدام اليومي.',
                ],
                'description' => [
                    'en' => 'Suitable for coffee, cereals, baking, and everyday household use.',
                    'ar' => 'مناسب للقهوة، والأرز، والخبز، والاستخدامات المنزلية اليومية.',
                ],
                'image' => 'dashboard/app-assets/images/slider/07.jpg',
                'price' => 34,
                'sku' => 'DAIRY-MILK-1L',
                'stock' => 80,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Dairy',
                'name' => [
                    'en' => 'Greek Yogurt',
                    'ar' => 'زبادي اليوناني',
                ],
                'short_description' => [
                    'en' => 'Rich and creamy yogurt cup.',
                    'ar' => 'كوب زبادي غني وكريمي.',
                ],
                'description' => [
                    'en' => 'High-protein yogurt with a thick texture for breakfast bowls and snacks.',
                    'ar' => 'زبادي عالي البروتين مع نسيج سميك لطبقات الإفطار والوجبات الخفيفة.',
                ],
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
                'name' => [
                    'en' => 'Fresh Lemons 1kg',
                    'ar' => 'برتقال طازج 1 كجم',
                ],
                'short_description' => [
                    'en' => 'Bright and juicy lemons sold by the kilo.',
                    'ar' => 'برتقال بارز وجذاب مباع بالكيلو.',
                ],
                'description' => [
                    'en' => 'Ideal for juices, marinades, tea, and everyday kitchen prep.',
                    'ar' => 'مثالي للعصائر، والمرشحات، والشاي، واستخدامات المطبخ اليومية.',
                ],
                'image' => 'dashboard/app-assets/images/slider/09.jpg',
                'price' => 15,
                'sku' => 'FRUIT-LEMON-1KG',
                'stock' => 100,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Imported Fruits',
                'name' => [
                    'en' => 'Imported Avocado',
                    'ar' => 'أوكادو المستورد',
                ],
                'short_description' => [
                    'en' => 'Premium imported avocados.',
                    'ar' => 'أوكادو مستورد عالي الجودة.',
                ],
                'description' => [
                    'en' => 'Creamy imported avocados suitable for salads, sandwiches, and healthy bowls.',
                    'ar' => 'أوكادو مستورد كريمي مناسب للسلطات، والساندويتشات، والطبقات الصحية.',
                ],
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
                'name' => [
                    'en' => 'Baby Spinach',
                    'ar' => 'سبانخ الرضيع',
                ],
                'short_description' => [
                    'en' => 'Fresh baby spinach leaves.',
                    'ar' => 'أوراق سبانخ الرضيع الطازجة.',
                ],
                'description' => [
                    'en' => 'Tender leafy greens ready for salads, sauteing, or smoothie prep.',
                    'ar' => 'خضروات عليا ناعمة جاهزة للسلطات، والطهي، أو إعداد العصائر.',
                ],
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'price' => 22,
                'sku' => 'VEG-SPINACH-BABY',
                'stock' => 35,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'category' => 'Cleaning Supplies',
                'name' => [
                    'en' => 'Laundry Detergent 2.5L',
                    'ar' => 'مغسلة للغسيل 2.5 لتر',
                ],
                'short_description' => [
                    'en' => 'Concentrated detergent for everyday loads.',
                    'ar' => 'مغسلة مركزة للغسيل اليومي.',
                ],
                'description' => [
                    'en' => 'Large-format detergent for colored and mixed fabrics with fresh fragrance.',
                    'ar' => 'مغسلة بتنسيق كبير للنسيج الملون والمتداخل مع عطر طازج.',
                ],
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
            $category = Category::query()
                ->where('slug', Str::slug($productData['category']))
                ->firstOrFail();

            $product = Product::query()->firstOrNew([
                'slug' => Str::slug($productData['name']['en']),
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
