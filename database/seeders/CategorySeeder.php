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
                'name' => 'Fruits',
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Citrus',
                        'image' => 'dashboard/app-assets/images/slider/02.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Imported Fruits',
                        'image' => 'dashboard/app-assets/images/slider/03.jpg',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Vegetables',
                'image' => 'dashboard/app-assets/images/slider/04.jpg',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Leafy Greens',
                        'image' => 'dashboard/app-assets/images/slider/05.jpg',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Beverages',
                'image' => 'dashboard/app-assets/images/slider/06.jpg',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Juice',
                        'image' => 'dashboard/app-assets/images/slider/07.jpg',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Soda',
                        'image' => 'dashboard/app-assets/images/slider/08.jpg',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Bakery',
                'image' => 'dashboard/app-assets/images/slider/09.jpg',
                'sort_order' => 4,
            ],
            [
                'name' => 'Snacks',
                'image' => 'dashboard/app-assets/images/slider/10.jpg',
                'sort_order' => 5,
            ],
            [
                'name' => 'Dairy',
                'image' => 'dashboard/app-assets/images/slider/01.jpg',
                'sort_order' => 6,
            ],
            [
                'name' => 'Cleaning Supplies',
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
