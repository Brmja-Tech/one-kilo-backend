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
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => [
                            'en' => 'Citrus',
                            'ar' => 'البرتقال',
                        ],
                        'image' => 'dashboard/app-assets/images/slider/02.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => [
                            'en' => 'Imported Fruits',
                            'ar' => 'الفواكه المستوردة',
                        ],
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
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => [
                            'en' => 'Leafy Greens',
                            'ar' => 'الخضروات الورقية',
                        ],
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
                'image' => 'dashboard/app-assets/images/slider/06.jpg',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => [
                            'en' => 'Juice',
                            'ar' => 'عصير',
                        ],
                        'image' => 'dashboard/app-assets/images/slider/07.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => [
                            'en' => 'Soda',
                            'ar' => ' soda',
                        ],
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
                'image' => 'dashboard/app-assets/images/slider/09.jpg',
                'sort_order' => 4,
            ],
            [
                'name' => [
                    'en' => 'Snacks',
                    'ar' => 'الوجبات الخفيفة',
                ],
                'image' => 'dashboard/app-assets/images/slider/10.jpg',
                'sort_order' => 5,
            ],
            [
                'name' => [
                    'en' => 'Dairy',
                    'ar' => 'الألبان',
                ],
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'sort_order' => 6,
            ],
            [
                'name' => [
                    'en' => 'Cleaning Supplies',
                    'ar' => 'معدات التنظيف',
                ],
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

        $category = Category::query()->firstOrNew([
            'name' => $categoryData['name'],
        ]);

        $category->fill([
            ...$categoryData,
            'parent_id' => $parent?->id,
            'status' => true,
        ]);
        $category->save();

        foreach ($children as $childData) {
            $this->syncCategory($childData, $category);
        }

        return $category;
    }
}
