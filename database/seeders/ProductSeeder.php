<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id', 'slug');
        $variantIds = Variant::query()->pluck('id', 'key');
        $variantItemIds = $this->buildVariantItemIdMap();

        $products = [
            // Existing demo products referenced by other seeders (favorites/cart/images).
            [
                'slug' => 'mango-juice-1l',
                'category_slug' => 'juice',
                'has_variants' => false,
                'name' => ['en' => 'Mango Juice 1L', 'ar' => 'عصير مانجو 1 لتر'],
                'short_description' => ['en' => 'Refreshing mango juice.', 'ar' => 'عصير مانجو منعش.'],
                'description' => ['en' => 'Chilled mango juice, perfect for any time.', 'ar' => 'عصير مانجو بارد مناسب لأي وقت.'],
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'price' => 45,
                'stock' => 120,
                'sku' => 'JUICE-MANGO-1L',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'slug' => 'sea-salt-chips',
                'category_slug' => 'snacks',
                'has_variants' => false,
                'name' => ['en' => 'Sea Salt Chips', 'ar' => 'شيبسي ملح البحر'],
                'short_description' => ['en' => 'Crunchy and lightly salted.', 'ar' => 'مقرمش ومملح قليلاً.'],
                'description' => ['en' => 'Classic chips with sea salt.', 'ar' => 'شيبسي بطعم ملح البحر.'],
                'image' => 'dashboard/app-assets/images/slider/06.jpg',
                'price' => 25,
                'stock' => 200,
                'sku' => 'SNACK-CHIPS-SEA-SALT',
                'is_featured' => false,
                'status' => true,
            ],
            [
                'slug' => 'butter-croissant',
                'category_slug' => 'bakery',
                'has_variants' => false,
                'name' => ['en' => 'Butter Croissant', 'ar' => 'كرواسون زبدة'],
                'short_description' => ['en' => 'Freshly baked croissant.', 'ar' => 'كرواسون طازج.'],
                'description' => ['en' => 'Buttery, flaky croissant baked daily.', 'ar' => 'كرواسون هش بالزبدة مخبوز يوميًا.'],
                'image' => 'dashboard/app-assets/images/slider/09.jpg',
                'price' => 18,
                'stock' => 80,
                'sku' => 'BAKERY-CROISSANT-BUTTER',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'slug' => 'full-cream-milk-1l',
                'category_slug' => 'dairy',
                'has_variants' => false,
                'name' => ['en' => 'Full Cream Milk 1L', 'ar' => 'حليب كامل الدسم 1 لتر'],
                'short_description' => ['en' => 'Rich full cream milk.', 'ar' => 'حليب كامل الدسم غني.'],
                'description' => ['en' => 'Full cream milk 1 liter.', 'ar' => 'حليب كامل الدسم سعة 1 لتر.'],
                'image' => 'dashboard/app-assets/images/slider/08.jpg',
                'price' => 35,
                'stock' => 140,
                'sku' => 'DAIRY-MILK-FULL-1L',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'slug' => 'greek-yogurt',
                'category_slug' => 'dairy',
                'has_variants' => false,
                'name' => ['en' => 'Greek Yogurt', 'ar' => 'زبادي يوناني'],
                'short_description' => ['en' => 'Creamy greek yogurt.', 'ar' => 'زبادي يوناني كريمي.'],
                'description' => ['en' => 'High-protein greek yogurt.', 'ar' => 'زبادي يوناني عالي البروتين.'],
                'image' => 'dashboard/app-assets/images/slider/05.jpg',
                'price' => 22,
                'stock' => 90,
                'sku' => 'DAIRY-YOGURT-GREEK',
                'is_featured' => false,
                'status' => true,
            ],
            [
                'slug' => 'imported-avocado',
                'category_slug' => 'imported-fruits',
                'has_variants' => false,
                'name' => ['en' => 'Imported Avocado', 'ar' => 'أفوكادو مستورد'],
                'short_description' => ['en' => 'Fresh imported avocado.', 'ar' => 'أفوكادو مستورد طازج.'],
                'description' => ['en' => 'Perfect for salads and toast.', 'ar' => 'مناسب للسلطات والتوست.'],
                'image' => 'dashboard/app-assets/images/slider/03.jpg',
                'price' => 60,
                'stock' => 60,
                'sku' => 'FRUIT-AVOCADO-IMPORTED',
                'is_featured' => false,
                'status' => true,
            ],
            [
                'slug' => 'laundry-detergent-25l',
                'category_slug' => 'cleaning-supplies',
                'has_variants' => false,
                'name' => ['en' => 'Laundry Detergent 2.5L', 'ar' => 'منظف غسيل 2.5 لتر'],
                'short_description' => ['en' => 'Fresh scent detergent.', 'ar' => 'منظف برائحة منعشة.'],
                'description' => ['en' => 'Powerful laundry detergent 2.5L.', 'ar' => 'منظف غسيل قوي سعة 2.5 لتر.'],
                'image' => 'dashboard/app-assets/images/slider/10.jpg',
                'price' => 120,
                'stock' => 40,
                'sku' => 'CLEAN-DETERGENT-25L',
                'is_featured' => false,
                'status' => true,
            ],

            // FIX-style variants examples.
            [
                'slug' => 'tomatoes',
                'category_slug' => 'vegetables',
                'has_variants' => true,
                'name' => ['en' => 'Tomatoes', 'ar' => 'طماطم'],
                'short_description' => ['en' => 'Fresh red tomatoes.', 'ar' => 'طماطم حمراء طازجة.'],
                'description' => ['en' => 'Locally sourced tomatoes.', 'ar' => 'طماطم من مصادر محلية.'],
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'status' => true,
                'sku_setup' => [
                    ['sku' => 'TOM-250GM', 'sort_order' => 1, 'price' => 14.99, 'quantity' => 80, 'attributes' => ['weight' => '250 gm']],
                    ['sku' => 'TOM-500GM', 'sort_order' => 2, 'price' => 27.99, 'quantity' => 60, 'attributes' => ['weight' => '500 gm']],
                    ['sku' => 'TOM-1KG', 'sort_order' => 3, 'price' => 49.99, 'quantity' => 40, 'attributes' => ['weight' => '1 kg']],
                ],
            ],
            [
                'slug' => 'bananas',
                'category_slug' => 'fruits',
                'has_variants' => true,
                'name' => ['en' => 'Bananas', 'ar' => 'موز'],
                'short_description' => ['en' => 'Sweet ripe bananas.', 'ar' => 'موز ناضج حلو.'],
                'description' => ['en' => 'Perfect for snacks and smoothies.', 'ar' => 'مناسب للسناكس والعصائر.'],
                'image' => 'dashboard/app-assets/images/slider/02.jpg',
                'status' => true,
                'sku_setup' => [
                    ['sku' => 'BAN-500GM', 'sort_order' => 1, 'price' => 24.99, 'quantity' => 70, 'attributes' => ['weight' => '500 gm']],
                    ['sku' => 'BAN-1KG', 'sort_order' => 2, 'price' => 44.99, 'quantity' => 50, 'attributes' => ['weight' => '1 kg']],
                ],
            ],
            [
                'slug' => 'eggs',
                'category_slug' => 'dairy',
                'has_variants' => true,
                'name' => ['en' => 'Eggs', 'ar' => 'بيض'],
                'short_description' => ['en' => 'Farm fresh eggs.', 'ar' => 'بيض طازج من المزرعة.'],
                'description' => ['en' => 'Choose the pack size that suits you.', 'ar' => 'اختر حجم العبوة المناسب لك.'],
                'image' => 'dashboard/app-assets/images/slider/07.jpg',
                'status' => true,
                'sku_setup' => [
                    ['sku' => 'EGG-6PCS', 'sort_order' => 1, 'price' => 39.99, 'quantity' => 100, 'attributes' => ['package' => '6 pcs']],
                    ['sku' => 'EGG-12PCS', 'sort_order' => 2, 'price' => 74.99, 'quantity' => 60, 'attributes' => ['package' => '12 pcs']],
                    ['sku' => 'EGG-TRAY', 'sort_order' => 3, 'price' => 199.99, 'quantity' => 30, 'attributes' => ['package' => 'Tray']],
                ],
            ],
            [
                'slug' => 'water',
                'category_slug' => 'beverages',
                'has_variants' => true,
                'name' => ['en' => 'Mineral Water', 'ar' => 'مياه معدنية'],
                'short_description' => ['en' => 'Clean and refreshing.', 'ar' => 'نقية ومنعشة.'],
                'description' => ['en' => 'Different packages for home and on-the-go.', 'ar' => 'عبوات مختلفة للبيت وللتنقل.'],
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'status' => true,
                'sku_setup' => [
                    ['sku' => 'WATER-BOTTLE', 'sort_order' => 1, 'price' => 7.99, 'quantity' => 300, 'attributes' => ['package' => 'Bottle']],
                    ['sku' => 'WATER-PACK', 'sort_order' => 2, 'price' => 39.99, 'quantity' => 120, 'attributes' => ['package' => 'Pack']],
                    ['sku' => 'WATER-CARTON', 'sort_order' => 3, 'price' => 74.99, 'quantity' => 70, 'attributes' => ['package' => 'Carton']],
                ],
            ],
            [
                'slug' => 'fresh-milk',
                'category_slug' => 'dairy',
                'has_variants' => false,
                'name' => ['en' => 'Fresh Milk', 'ar' => 'حليب طازج'],
                'short_description' => ['en' => 'Daily fresh milk.', 'ar' => 'حليب طازج يوميًا.'],
                'description' => ['en' => 'Simple product example (no variants).', 'ar' => 'مثال لمنتج بسيط (بدون خيارات).'],
                'image' => 'dashboard/app-assets/images/slider/08.jpg',
                'price' => 29.99,
                'stock' => 100,
                'sku' => 'DAIRY-FRESH-MILK',
                'is_featured' => false,
                'status' => true,
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);

            $skuSetup = $productData['sku_setup'] ?? [];
            unset($productData['sku_setup']);

            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $this->resolveCategoryId($categoryIds, $categorySlug),
                    'name' => $productData['name'],
                    'short_description' => $productData['short_description'] ?? null,
                    'description' => $productData['description'] ?? null,
                    'image' => $productData['image'],
                    'price' => $productData['has_variants'] ? null : ($productData['price'] ?? null),
                    'discount_type' => $productData['discount_type'] ?? null,
                    'discount_value' => $productData['discount_value'] ?? null,
                    'discount_starts_at' => $productData['discount_starts_at'] ?? null,
                    'discount_ends_at' => $productData['discount_ends_at'] ?? null,
                    'sku' => $productData['has_variants'] ? null : ($productData['sku'] ?? null),
                    'stock' => $productData['has_variants'] ? 0 : ($productData['stock'] ?? 0),
                    'is_featured' => (bool) ($productData['is_featured'] ?? false),
                    'has_variants' => (bool) ($productData['has_variants'] ?? false),
                    'status' => (bool) ($productData['status'] ?? true),
                ]
            );

            if (! $product->has_variants) {
                ProductSku::query()->where('product_id', $product->id)->delete();
                continue;
            }

            $this->syncProductSkus($product, $skuSetup, $variantIds, $variantItemIds);
        }
    }

    /**
     * @param  Collection<string, int>  $categoryIds
     */
    private function resolveCategoryId(Collection $categoryIds, string $slug): int
    {
        $id = $categoryIds->get($slug);

        if ($id) {
            return (int) $id;
        }

        return (int) Category::query()->where('slug', $slug)->value('id');
    }

    private function buildVariantItemIdMap(): array
    {
        $items = VariantItem::query()
            ->select(['variant_items.id', 'variant_items.variant_id', 'variant_items.name_plain'])
            ->with('variant:id,key')
            ->get();

        $map = [];

        foreach ($items as $item) {
            $variantKey = $item->variant?->key;
            $namePlain = trim((string) ($item->name_plain ?? ''));

            if (! $variantKey || $namePlain === '') {
                continue;
            }

            $map[$variantKey][strtolower($namePlain)] = (int) $item->id;
        }

        return $map;
    }

    /**
     * @param  Collection<string, int>  $variantIds
     */
    private function syncProductSkus(
        Product $product,
        array $skuSetup,
        Collection $variantIds,
        array $variantItemIds
    ): void {
        $signatures = [];

        foreach ($skuSetup as $skuData) {
            if (! is_array($skuData) || empty($skuData['attributes'])) {
                continue;
            }

            $attributesPayload = [];

            foreach ((array) $skuData['attributes'] as $variantKey => $itemPlain) {
                $variantKey = trim((string) $variantKey);
                $itemPlain = trim((string) $itemPlain);

                if ($variantKey === '' || $itemPlain === '') {
                    continue;
                }

                $variantId = (int) ($variantIds->get($variantKey) ?? 0);

                if (! $variantId) {
                    $variantId = (int) Variant::query()->where('key', $variantKey)->value('id');
                }

                if (! $variantId) {
                    continue;
                }

                $itemId = (int) ($variantItemIds[$variantKey][strtolower($itemPlain)] ?? 0);

                if (! $itemId) {
                    $itemId = (int) VariantItem::query()
                        ->where('variant_id', $variantId)
                        ->where('name_plain', $itemPlain)
                        ->value('id');
                }

                if (! $itemId) {
                    continue;
                }

                $attributesPayload[] = [
                    'variant_id' => $variantId,
                    'variant_item_id' => $itemId,
                ];
            }

            $signature = ProductSku::signatureFromAttributes($attributesPayload);

            if ($signature === '') {
                continue;
            }

            $sku = ProductSku::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'signature' => $signature,
                ],
                [
                    'sku' => $skuData['sku'] ?? null,
                    'image' => null,
                    'price' => round((float) ($skuData['price'] ?? 0), 2),
                    'quantity' => (int) ($skuData['quantity'] ?? 0),
                    'status' => true,
                    'sort_order' => (int) ($skuData['sort_order'] ?? 0),
                ]
            );

            $sku->items()->delete();

            foreach ($attributesPayload as $attribute) {
                $sku->items()->create($attribute);
            }

            $signatures[] = $signature;
        }

        if ($signatures === []) {
            return;
        }

        ProductSku::query()
            ->where('product_id', $product->id)
            ->whereNotIn('signature', $signatures)
            ->delete();
    }
}
