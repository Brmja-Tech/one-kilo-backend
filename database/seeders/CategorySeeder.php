<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Fruits',
                    'ar' => 'الفواكه',
                ],
                'slug' => 'fruits',
                'color' => '0xFFE4572E',
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => [
                            'en' => 'Citrus',
                            'ar' => 'البرتقال',
                        ],
                        'slug' => 'citrus',
                        'color' => '0xFFFFB703',
                        'image' => 'dashboard/app-assets/images/slider/02.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => [
                            'en' => 'Imported Fruits',
                            'ar' => 'الفواكه المستوردة',
                        ],
                        'slug' => 'imported-fruits',
                        'color' => '0xFF2A9D8F',
                        'image' => 'dashboard/app-assets/images/slider/03.jpg',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Vegetables',
                    'ar' => 'الخضروات',
                ],
                'slug' => 'vegetables',
                'color' => '0xFF2E7D32',
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => [
                            'en' => 'Leafy Greens',
                            'ar' => 'الخضروات الورقية',
                        ],
                        'slug' => 'leafy-greens',
                        'color' => '0xFF4CAF50',
                        'image' => 'dashboard/app-assets/images/slider/05.jpg',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Beverages',
                    'ar' => 'المشروبات',
                ],
                'slug' => 'beverages',
                'color' => '0xFF1946B9',
                'image' => 'dashboard/app-assets/images/slider/06.jpg',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => [
                            'en' => 'Juice',
                            'ar' => 'عصير',
                        ],
                        'slug' => 'juice',
                        'color' => '0xFFFF9800',
                        'image' => 'dashboard/app-assets/images/slider/07.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => [
                            'en' => 'Soda',
                            'ar' => ' soda',
                        ],
                        'slug' => 'soda',
                        'color' => '0xFFC62828',
                        'image' => 'dashboard/app-assets/images/slider/08.jpg',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Bakery',
                    'ar' => 'المخبوزات',
                ],
                'slug' => 'bakery',
                'color' => '0xFFA47148',
                'image' => 'dashboard/app-assets/images/slider/09.jpg',
                'sort_order' => 4,
            ],
            [
                'name' => [
                    'en' => 'Snacks',
                    'ar' => 'الوجبات الخفيفة',
                ],
                'slug' => 'snacks',
                'color' => '0xFFF4A261',
                'image' => 'dashboard/app-assets/images/slider/10.jpg',
                'sort_order' => 5,
            ],
            [
                'name' => [
                    'en' => 'Dairy',
                    'ar' => 'الألبان',
                ],
                'slug' => 'dairy',
                'color' => '0xFF81D4FA',
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'sort_order' => 6,
            ],
            [
                'name' => [
                    'en' => 'Cleaning Supplies',
                    'ar' => 'معدات التنظيف',
                ],
                'slug' => 'cleaning-supplies',
                'color' => '0xFF607D8B',
                'image' => 'dashboard/app-assets/images/slider/02.jpg',
                'sort_order' => 7,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->syncCategory($categoryData);
        }
    }

    private function syncCategory(array $categoryData, ?Category $parent = null): Category
    {
        $children = $categoryData['children'] ?? [];
        unset($categoryData['children']);

        $category = Category::query()->updateOrCreate(
            ['slug' => $categoryData['slug']],
            [
                ...$categoryData,
                'parent_id' => $parent?->id,
                'status' => true,
            ]
        );

        foreach ($children as $childData) {
            $this->syncCategory($childData, $category);
        }

        return $category;
    }
}
