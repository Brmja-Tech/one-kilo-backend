<?php

namespace Database\Seeders;

use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            [
                'key' => 'weight',
                'sort_order' => 1,
                'name' => ['ar' => 'الوزن', 'en' => 'Weight'],
                'items' => [
                    ['sort_order' => 1, 'name_plain' => '250 gm', 'name' => ['ar' => '250 جرام', 'en' => '250 gm']],
                    ['sort_order' => 2, 'name_plain' => '500 gm', 'name' => ['ar' => '500 جرام', 'en' => '500 gm']],
                    ['sort_order' => 3, 'name_plain' => '1 kg', 'name' => ['ar' => '1 كيلو', 'en' => '1 kg']],
                ],
            ],
            [
                'key' => 'package',
                'sort_order' => 2,
                'name' => ['ar' => 'العبوة', 'en' => 'Package'],
                'items' => [
                    ['sort_order' => 1, 'name_plain' => '6 pcs', 'name' => ['ar' => '6 بيضات', 'en' => '6 pcs']],
                    ['sort_order' => 2, 'name_plain' => '12 pcs', 'name' => ['ar' => '12 بيضة', 'en' => '12 pcs']],
                    ['sort_order' => 3, 'name_plain' => 'Tray', 'name' => ['ar' => 'طبق', 'en' => 'Tray']],
                    ['sort_order' => 4, 'name_plain' => 'Bottle', 'name' => ['ar' => 'زجاجة', 'en' => 'Bottle']],
                    ['sort_order' => 5, 'name_plain' => 'Pack', 'name' => ['ar' => 'باك', 'en' => 'Pack']],
                    ['sort_order' => 6, 'name_plain' => 'Carton', 'name' => ['ar' => 'كرتونة', 'en' => 'Carton']],
                ],
            ],
        ];

        foreach ($variants as $variantData) {
            $items = $variantData['items'] ?? [];
            unset($variantData['items']);

            $variant = Variant::query()->updateOrCreate(
                ['key' => $variantData['key']],
                [
                    'name' => $variantData['name'],
                    'status' => true,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                ]
            );

            foreach ($items as $itemData) {
                $namePlain = trim((string) ($itemData['name_plain'] ?? ''));

                if ($namePlain === '') {
                    continue;
                }

                VariantItem::query()->updateOrCreate(
                    [
                        'variant_id' => $variant->id,
                        'name_plain' => $namePlain,
                    ],
                    [
                        'variant_id' => $variant->id,
                        'name' => $itemData['name'],
                        'meta' => $itemData['meta'] ?? null,
                        'status' => true,
                        'sort_order' => $itemData['sort_order'] ?? 0,
                    ]
                );
            }
        }
    }
}

