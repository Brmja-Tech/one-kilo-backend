<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Database\Seeder;

class EgyptLocationSeeder extends Seeder
{
    public function run(): void
    {
        $egypt = Country::query()->updateOrCreate(
            ['name->en' => 'Egypt'],
            [
                'name' => [
                    'ar' => 'مصر',
                    'en' => 'Egypt',
                ],
                'status' => true,
            ]
        );

        foreach ($this->governoratesDataset() as $governorateEn => $data) {
            $governorate = Governorate::query()->updateOrCreate(
                [
                    'country_id' => $egypt->id,
                    'name->en' => $governorateEn,
                ],
                [
                    'country_id' => $egypt->id,
                    'name' => [
                        'ar' => $data['ar'],
                        'en' => $governorateEn,
                    ],
                    'status' => true,
                ]
            );

            foreach ($data['regions'] as $regionData) {
                Region::query()->updateOrCreate(
                    [
                        'governorate_id' => $governorate->id,
                        'name->en' => $regionData['en'],
                    ],
                    [
                        'governorate_id' => $governorate->id,
                        'name' => [
                            'ar' => $regionData['ar'],
                            'en' => $regionData['en'],
                        ],
                        'shipping_price' => $regionData['shipping_price'],
                        'status' => true,
                    ]
                );
            }
        }
    }

    private function governoratesDataset(): array
    {
        return [
            'Cairo' => [
                'ar' => 'القاهرة',
                'regions' => [
                    ['en' => 'Nasr City', 'ar' => 'مدينة نصر', 'shipping_price' => 35],
                    ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة', 'shipping_price' => 35],
                    ['en' => 'Maadi', 'ar' => 'المعادي', 'shipping_price' => 35],
                    ['en' => 'New Cairo', 'ar' => 'القاهرة الجديدة', 'shipping_price' => 40],
                    ['en' => 'Shubra', 'ar' => 'شبرا', 'shipping_price' => 35],
                    ['en' => 'Downtown', 'ar' => 'وسط البلد', 'shipping_price' => 35],
                    ['en' => 'Helwan', 'ar' => 'حلوان', 'shipping_price' => 40],
                    ['en' => 'Mokattam', 'ar' => 'المقطم', 'shipping_price' => 40],
                ],
            ],
            'Giza' => [
                'ar' => 'الجيزة',
                'regions' => [
                    ['en' => 'Dokki', 'ar' => 'الدقي', 'shipping_price' => 35],
                    ['en' => 'Mohandessin', 'ar' => 'المهندسين', 'shipping_price' => 35],
                    ['en' => 'Agouza', 'ar' => 'العجوزة', 'shipping_price' => 35],
                    ['en' => 'Haram', 'ar' => 'الهرم', 'shipping_price' => 35],
                    ['en' => 'Faisal', 'ar' => 'فيصل', 'shipping_price' => 35],
                    ['en' => '6th of October', 'ar' => '6 أكتوبر', 'shipping_price' => 40],
                    ['en' => 'Sheikh Zayed', 'ar' => 'الشيخ زايد', 'shipping_price' => 40],
                    ['en' => 'Imbaba', 'ar' => 'إمبابة', 'shipping_price' => 35],
                ],
            ],
            'Alexandria' => [
                'ar' => 'الإسكندرية',
                'regions' => [
                    ['en' => 'Smouha', 'ar' => 'سموحة', 'shipping_price' => 45],
                    ['en' => 'Sidi Gaber', 'ar' => 'سيدي جابر', 'shipping_price' => 45],
                    ['en' => 'Miami', 'ar' => 'ميامي', 'shipping_price' => 45],
                    ['en' => 'El Montaza', 'ar' => 'المنتزه', 'shipping_price' => 45],
                    ['en' => 'El Agami', 'ar' => 'العجمي', 'shipping_price' => 45],
                    ['en' => 'Borg El Arab', 'ar' => 'برج العرب', 'shipping_price' => 50],
                ],
            ],
            'Dakahlia' => [
                'ar' => 'الدقهلية',
                'regions' => [
                    ['en' => 'Mansoura', 'ar' => 'المنصورة', 'shipping_price' => 45],
                    ['en' => 'Talkha', 'ar' => 'طلخا', 'shipping_price' => 45],
                    ['en' => 'Mit Ghamr', 'ar' => 'ميت غمر', 'shipping_price' => 45],
                    ['en' => 'Belqas', 'ar' => 'بلقاس', 'shipping_price' => 45],
                    ['en' => 'Sherbin', 'ar' => 'شربين', 'shipping_price' => 45],
                    ['en' => 'Nabroh', 'ar' => 'نبروه', 'shipping_price' => 45],
                ],
            ],
            'Sharqia' => [
                'ar' => 'الشرقية',
                'regions' => [
                    ['en' => 'Zagazig', 'ar' => 'الزقازيق', 'shipping_price' => 45],
                    ['en' => 'Belbeis', 'ar' => 'بلبيس', 'shipping_price' => 45],
                    ['en' => '10th of Ramadan', 'ar' => 'العاشر من رمضان', 'shipping_price' => 45],
                    ['en' => 'Abu Kabir', 'ar' => 'أبو كبير', 'shipping_price' => 45],
                    ['en' => 'Faqous', 'ar' => 'فاقوس', 'shipping_price' => 45],
                    ['en' => 'Minya Al Qamh', 'ar' => 'منيا القمح', 'shipping_price' => 45],
                ],
            ],
            'Gharbia' => [
                'ar' => 'الغربية',
                'regions' => [
                    ['en' => 'Tanta', 'ar' => 'طنطا', 'shipping_price' => 45],
                    ['en' => 'El Mahalla El Kubra', 'ar' => 'المحلة الكبرى', 'shipping_price' => 45],
                    ['en' => 'Kafr El Zayat', 'ar' => 'كفر الزيات', 'shipping_price' => 45],
                    ['en' => 'Zefta', 'ar' => 'زفتى', 'shipping_price' => 45],
                    ['en' => 'Samannoud', 'ar' => 'سمنود', 'shipping_price' => 45],
                ],
            ],
            'Monufia' => [
                'ar' => 'المنوفية',
                'regions' => [
                    ['en' => 'Shebin El Kom', 'ar' => 'شبين الكوم', 'shipping_price' => 45],
                    ['en' => 'Menouf', 'ar' => 'منوف', 'shipping_price' => 45],
                    ['en' => 'Sadat City', 'ar' => 'مدينة السادات', 'shipping_price' => 45],
                    ['en' => 'Ashmoun', 'ar' => 'أشمون', 'shipping_price' => 45],
                    ['en' => 'Quesna', 'ar' => 'قويسنا', 'shipping_price' => 45],
                ],
            ],
            'Qalyubia' => [
                'ar' => 'القليوبية',
                'regions' => [
                    ['en' => 'Benha', 'ar' => 'بنها', 'shipping_price' => 35],
                    ['en' => 'Shubra El Kheima', 'ar' => 'شبرا الخيمة', 'shipping_price' => 35],
                    ['en' => 'Qalyub', 'ar' => 'قليوب', 'shipping_price' => 35],
                    ['en' => 'El Khanka', 'ar' => 'الخانكة', 'shipping_price' => 35],
                    ['en' => 'Obour City', 'ar' => 'العبور', 'shipping_price' => 40],
                    ['en' => 'Tukh', 'ar' => 'طوخ', 'shipping_price' => 35],
                ],
            ],
            'Beheira' => [
                'ar' => 'البحيرة',
                'regions' => [
                    ['en' => 'Damanhour', 'ar' => 'دمنهور', 'shipping_price' => 45],
                    ['en' => 'Kafr El Dawwar', 'ar' => 'كفر الدوار', 'shipping_price' => 45],
                    ['en' => 'Rashid', 'ar' => 'رشيد', 'shipping_price' => 50],
                    ['en' => 'Edku', 'ar' => 'إدكو', 'shipping_price' => 50],
                    ['en' => 'Itay El Barud', 'ar' => 'إيتاي البارود', 'shipping_price' => 45],
                ],
            ],
            'Kafr El Sheikh' => [
                'ar' => 'كفر الشيخ',
                'regions' => [
                    ['en' => 'Kafr El Sheikh City', 'ar' => 'كفر الشيخ', 'shipping_price' => 50],
                    ['en' => 'Desouk', 'ar' => 'دسوق', 'shipping_price' => 50],
                    ['en' => 'Baltim', 'ar' => 'بلطيم', 'shipping_price' => 55],
                    ['en' => 'Sidi Salem', 'ar' => 'سيدي سالم', 'shipping_price' => 50],
                    ['en' => 'Metoubes', 'ar' => 'مطوبس', 'shipping_price' => 55],
                ],
            ],
            'Damietta' => [
                'ar' => 'دمياط',
                'regions' => [
                    ['en' => 'Damietta', 'ar' => 'دمياط', 'shipping_price' => 50],
                    ['en' => 'New Damietta', 'ar' => 'دمياط الجديدة', 'shipping_price' => 50],
                    ['en' => 'Ras El Bar', 'ar' => 'رأس البر', 'shipping_price' => 55],
                    ['en' => 'Faraskur', 'ar' => 'فارسكور', 'shipping_price' => 50],
                    ['en' => 'Zarqa', 'ar' => 'الزرقا', 'shipping_price' => 50],
                ],
            ],
            'Port Said' => [
                'ar' => 'بورسعيد',
                'regions' => [
                    ['en' => 'Port Said', 'ar' => 'بورسعيد', 'shipping_price' => 50],
                    ['en' => 'Port Fouad', 'ar' => 'بورفؤاد', 'shipping_price' => 50],
                    ['en' => 'El Zohour', 'ar' => 'الزهور', 'shipping_price' => 50],
                    ['en' => 'El Manakh', 'ar' => 'المناخ', 'shipping_price' => 50],
                ],
            ],
            'Ismailia' => [
                'ar' => 'الإسماعيلية',
                'regions' => [
                    ['en' => 'Ismailia', 'ar' => 'الإسماعيلية', 'shipping_price' => 50],
                    ['en' => 'Fayed', 'ar' => 'فايد', 'shipping_price' => 50],
                    ['en' => 'Qantara East', 'ar' => 'القنطرة شرق', 'shipping_price' => 50],
                    ['en' => 'Qantara West', 'ar' => 'القنطرة غرب', 'shipping_price' => 50],
                    ['en' => 'Abu Suweir', 'ar' => 'أبو صوير', 'shipping_price' => 50],
                ],
            ],
            'Suez' => [
                'ar' => 'السويس',
                'regions' => [
                    ['en' => 'Suez', 'ar' => 'السويس', 'shipping_price' => 45],
                    ['en' => 'Ain Sokhna', 'ar' => 'العين السخنة', 'shipping_price' => 55],
                    ['en' => 'Arbaeen', 'ar' => 'الأربعين', 'shipping_price' => 45],
                    ['en' => 'Faisal District', 'ar' => 'فيصل', 'shipping_price' => 45],
                ],
            ],
            'Fayoum' => [
                'ar' => 'الفيوم',
                'regions' => [
                    ['en' => 'Fayoum', 'ar' => 'الفيوم', 'shipping_price' => 45],
                    ['en' => 'Senoures', 'ar' => 'سنورس', 'shipping_price' => 45],
                    ['en' => 'Ibshaway', 'ar' => 'إبشواي', 'shipping_price' => 45],
                    ['en' => 'Tamiya', 'ar' => 'طامية', 'shipping_price' => 45],
                    ['en' => 'Etsa', 'ar' => 'إطسا', 'shipping_price' => 45],
                ],
            ],
            'Beni Suef' => [
                'ar' => 'بني سويف',
                'regions' => [
                    ['en' => 'Beni Suef', 'ar' => 'بني سويف', 'shipping_price' => 50],
                    ['en' => 'Biba', 'ar' => 'ببا', 'shipping_price' => 50],
                    ['en' => 'Al Wasta', 'ar' => 'الواسطى', 'shipping_price' => 50],
                    ['en' => 'Nasser', 'ar' => 'ناصر', 'shipping_price' => 50],
                    ['en' => 'Fashn', 'ar' => 'الفشن', 'shipping_price' => 55],
                ],
            ],
            'Minya' => [
                'ar' => 'المنيا',
                'regions' => [
                    ['en' => 'Minya', 'ar' => 'المنيا', 'shipping_price' => 55],
                    ['en' => 'Mallawi', 'ar' => 'ملوي', 'shipping_price' => 55],
                    ['en' => 'Samalout', 'ar' => 'سمالوط', 'shipping_price' => 55],
                    ['en' => 'Beni Mazar', 'ar' => 'بني مزار', 'shipping_price' => 55],
                    ['en' => 'Abu Qurqas', 'ar' => 'أبو قرقاص', 'shipping_price' => 55],
                ],
            ],
            'Assiut' => [
                'ar' => 'أسيوط',
                'regions' => [
                    ['en' => 'Assiut', 'ar' => 'أسيوط', 'shipping_price' => 55],
                    ['en' => 'Dairut', 'ar' => 'ديروط', 'shipping_price' => 55],
                    ['en' => 'Manfalut', 'ar' => 'منفلوط', 'shipping_price' => 55],
                    ['en' => 'Abnub', 'ar' => 'أبنوب', 'shipping_price' => 55],
                    ['en' => 'Abu Tig', 'ar' => 'أبو تيج', 'shipping_price' => 55],
                ],
            ],
            'Sohag' => [
                'ar' => 'سوهاج',
                'regions' => [
                    ['en' => 'Sohag', 'ar' => 'سوهاج', 'shipping_price' => 55],
                    ['en' => 'Akhmim', 'ar' => 'أخميم', 'shipping_price' => 55],
                    ['en' => 'Girga', 'ar' => 'جرجا', 'shipping_price' => 55],
                    ['en' => 'Tahta', 'ar' => 'طهطا', 'shipping_price' => 55],
                    ['en' => 'El Balyana', 'ar' => 'البلينا', 'shipping_price' => 55],
                ],
            ],
            'Qena' => [
                'ar' => 'قنا',
                'regions' => [
                    ['en' => 'Qena', 'ar' => 'قنا', 'shipping_price' => 60],
                    ['en' => 'Nag Hammadi', 'ar' => 'نجع حمادي', 'shipping_price' => 60],
                    ['en' => 'Qus', 'ar' => 'قوص', 'shipping_price' => 60],
                    ['en' => 'Dishna', 'ar' => 'دشنا', 'shipping_price' => 60],
                    ['en' => 'Naqada', 'ar' => 'نقادة', 'shipping_price' => 60],
                ],
            ],
            'Luxor' => [
                'ar' => 'الأقصر',
                'regions' => [
                    ['en' => 'Luxor', 'ar' => 'الأقصر', 'shipping_price' => 60],
                    ['en' => 'Karnak', 'ar' => 'الكرنك', 'shipping_price' => 60],
                    ['en' => 'Esna', 'ar' => 'إسنا', 'shipping_price' => 60],
                    ['en' => 'Armant', 'ar' => 'أرمنت', 'shipping_price' => 60],
                ],
            ],
            'Aswan' => [
                'ar' => 'أسوان',
                'regions' => [
                    ['en' => 'Aswan', 'ar' => 'أسوان', 'shipping_price' => 65],
                    ['en' => 'Edfu', 'ar' => 'إدفو', 'shipping_price' => 65],
                    ['en' => 'Kom Ombo', 'ar' => 'كوم أمبو', 'shipping_price' => 65],
                    ['en' => 'Daraw', 'ar' => 'دراو', 'shipping_price' => 65],
                    ['en' => 'Abu Simbel', 'ar' => 'أبو سمبل', 'shipping_price' => 70],
                ],
            ],
            'Matrouh' => [
                'ar' => 'مطروح',
                'regions' => [
                    ['en' => 'Marsa Matrouh', 'ar' => 'مرسى مطروح', 'shipping_price' => 65],
                    ['en' => 'El Alamein', 'ar' => 'العلمين', 'shipping_price' => 65],
                    ['en' => 'Siwa', 'ar' => 'سيوة', 'shipping_price' => 70],
                    ['en' => 'El Dabaa', 'ar' => 'الضبعة', 'shipping_price' => 65],
                    ['en' => 'Sallum', 'ar' => 'السلوم', 'shipping_price' => 70],
                ],
            ],
            'Red Sea' => [
                'ar' => 'البحر الأحمر',
                'regions' => [
                    ['en' => 'Hurghada', 'ar' => 'الغردقة', 'shipping_price' => 60],
                    ['en' => 'El Gouna', 'ar' => 'الجونة', 'shipping_price' => 65],
                    ['en' => 'Safaga', 'ar' => 'سفاجا', 'shipping_price' => 65],
                    ['en' => 'Marsa Alam', 'ar' => 'مرسى علم', 'shipping_price' => 70],
                    ['en' => 'Ras Ghareb', 'ar' => 'رأس غارب', 'shipping_price' => 65],
                ],
            ],
            'North Sinai' => [
                'ar' => 'شمال سيناء',
                'regions' => [
                    ['en' => 'Arish', 'ar' => 'العريش', 'shipping_price' => 65],
                    ['en' => 'Sheikh Zuweid', 'ar' => 'الشيخ زويد', 'shipping_price' => 65],
                    ['en' => 'Rafah', 'ar' => 'رفح', 'shipping_price' => 65],
                    ['en' => 'Bir al-Abd', 'ar' => 'بئر العبد', 'shipping_price' => 65],
                    ['en' => 'Al Hasana', 'ar' => 'الحسنة', 'shipping_price' => 70],
                ],
            ],
            'South Sinai' => [
                'ar' => 'جنوب سيناء',
                'regions' => [
                    ['en' => 'Sharm El Sheikh', 'ar' => 'شرم الشيخ', 'shipping_price' => 75],
                    ['en' => 'Dahab', 'ar' => 'دهب', 'shipping_price' => 70],
                    ['en' => 'Nuweiba', 'ar' => 'نويبع', 'shipping_price' => 70],
                    ['en' => 'Taba', 'ar' => 'طابا', 'shipping_price' => 75],
                    ['en' => 'El Tor', 'ar' => 'الطور', 'shipping_price' => 70],
                    ['en' => 'Saint Catherine', 'ar' => 'سانت كاترين', 'shipping_price' => 75],
                ],
            ],
            'New Valley' => [
                'ar' => 'الوادي الجديد',
                'regions' => [
                    ['en' => 'Kharga', 'ar' => 'الخارجة', 'shipping_price' => 70],
                    ['en' => 'Dakhla', 'ar' => 'الداخلة', 'shipping_price' => 70],
                    ['en' => 'Farafra', 'ar' => 'الفرافرة', 'shipping_price' => 75],
                    ['en' => 'Balat', 'ar' => 'بلاط', 'shipping_price' => 70],
                    ['en' => 'Paris', 'ar' => 'باريس', 'shipping_price' => 75],
                ],
            ],
        ];
    }
}
