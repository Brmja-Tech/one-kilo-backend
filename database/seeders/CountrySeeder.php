<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => [
                    'ar' => 'مصر',
                    'en' => 'Egypt',
                ],
                'status' => true,
            ],
            [
                'name' => [
                    'ar' => 'المملكة العربية السعودية',
                    'en' => 'Saudi Arabia',
                ],
                'status' => true,
            ],
        ];

        foreach ($countries as $countryData) {
            Country::query()->updateOrCreate(
                ['name->en' => $countryData['name']['en']],
                $countryData
            );
        }
    }
}
