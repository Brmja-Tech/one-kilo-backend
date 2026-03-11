<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'banner' => 'uploads/images/logo.png',
                'status' => true,
            ],
            [
                'banner' => 'uploads/images/image.png',
                'status' => true,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::query()->updateOrCreate(
                ['banner' => $bannerData['banner']],
                $bannerData
            );
        }
    }
}
