<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $egyptId = Country::query()->where('name->en', 'Egypt')->value('id');
        $saudiId = Country::query()->where('name->en', 'Saudi Arabia')->value('id');

        if ($egyptId) {
            $this->seedGovernorates($egyptId, [
                ['en' => 'Cairo', 'ar' => 'القاهرة', 'shipping_price' => 35],
                ['en' => 'Giza', 'ar' => 'الجيزة', 'shipping_price' => 35],
                ['en' => 'Alexandria', 'ar' => 'الإسكندرية', 'shipping_price' => 45],
                ['en' => 'Dakahlia', 'ar' => 'الدقهلية', 'shipping_price' => 45],
                ['en' => 'Red Sea', 'ar' => 'البحر الأحمر', 'shipping_price' => 60],
                ['en' => 'Beheira', 'ar' => 'البحيرة', 'shipping_price' => 45],
                ['en' => 'Fayoum', 'ar' => 'الفيوم', 'shipping_price' => 45],
                ['en' => 'Gharbia', 'ar' => 'الغربية', 'shipping_price' => 45],
                ['en' => 'Ismailia', 'ar' => 'الإسماعيلية', 'shipping_price' => 50],
                ['en' => 'Menofia', 'ar' => 'المنوفية', 'shipping_price' => 45],
                ['en' => 'Minya', 'ar' => 'المنيا', 'shipping_price' => 55],
                ['en' => 'Qaliubia', 'ar' => 'القليوبية', 'shipping_price' => 35],
                ['en' => 'New Valley', 'ar' => 'الوادي الجديد', 'shipping_price' => 65],
                ['en' => 'Suez', 'ar' => 'السويس', 'shipping_price' => 45],
                ['en' => 'Aswan', 'ar' => 'أسوان', 'shipping_price' => 65],
                ['en' => 'Assiut', 'ar' => 'أسيوط', 'shipping_price' => 55],
                ['en' => 'Beni Suef', 'ar' => 'بني سويف', 'shipping_price' => 50],
                ['en' => 'Port Said', 'ar' => 'بورسعيد', 'shipping_price' => 50],
                ['en' => 'Damietta', 'ar' => 'دمياط', 'shipping_price' => 50],
                ['en' => 'Sharkia', 'ar' => 'الشرقية', 'shipping_price' => 45],
                ['en' => 'South Sinai', 'ar' => 'جنوب سيناء', 'shipping_price' => 65],
                ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ', 'shipping_price' => 50],
                ['en' => 'Matrouh', 'ar' => 'مطروح', 'shipping_price' => 65],
                ['en' => 'Luxor', 'ar' => 'الأقصر', 'shipping_price' => 60],
                ['en' => 'Qena', 'ar' => 'قنا', 'shipping_price' => 60],
                ['en' => 'North Sinai', 'ar' => 'شمال سيناء', 'shipping_price' => 65],
                ['en' => 'Sohag', 'ar' => 'سوهاج', 'shipping_price' => 55],
            ]);
        }

        if ($saudiId) {
            $this->seedGovernorates($saudiId, [
                ['en' => 'Riyadh', 'ar' => 'الرياض', 'shipping_price' => 45],
                ['en' => 'Makkah', 'ar' => 'مكة المكرمة', 'shipping_price' => 50],
                ['en' => 'Madinah', 'ar' => 'المدينة المنورة', 'shipping_price' => 50],
                ['en' => 'Eastern Province', 'ar' => 'المنطقة الشرقية', 'shipping_price' => 55],
                ['en' => 'Asir', 'ar' => 'عسير', 'shipping_price' => 60],
                ['en' => 'Tabuk', 'ar' => 'تبوك', 'shipping_price' => 60],
                ['en' => 'Hail', 'ar' => 'حائل', 'shipping_price' => 55],
                ['en' => 'Northern Borders', 'ar' => 'الحدود الشمالية', 'shipping_price' => 60],
                ['en' => 'Jazan', 'ar' => 'جازان', 'shipping_price' => 60],
                ['en' => 'Najran', 'ar' => 'نجران', 'shipping_price' => 60],
                ['en' => 'Al Bahah', 'ar' => 'الباحة', 'shipping_price' => 55],
                ['en' => 'Al Jawf', 'ar' => 'الجوف', 'shipping_price' => 55],
                ['en' => 'Al Qassim', 'ar' => 'القصيم', 'shipping_price' => 50],
            ]);
        }
    }

    private function seedGovernorates(int $countryId, array $items): void
    {
        foreach ($items as $item) {
            Governorate::query()->updateOrCreate(
                [
                    'country_id' => $countryId,
                    'name->en' => $item['en'],
                ],
                [
                    'country_id' => $countryId,
                    'name' => [
                        'ar' => $item['ar'],
                        'en' => $item['en'],
                    ],
                    'shipping_price' => $item['shipping_price'],
                    'status' => true,
                ]
            );
        }
    }
}
