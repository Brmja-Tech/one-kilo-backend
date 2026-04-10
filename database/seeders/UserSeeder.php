<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $egyptId = Country::query()->where('name->en', 'Egypt')->value('id');
        $cairoId = Governorate::query()
            ->where('country_id', $egyptId)
            ->where('name->en', 'Cairo')
            ->value('id');
        $nasrCityId = Region::query()
            ->where('governorate_id', $cairoId)
            ->where('name->en', 'Nasr City')
            ->value('id');

        $user = User::query()->firstOrNew(['email' => 'shopper@onekilo.test']);
        $user->image = 'uploads/images/image.png';
        $user->name = 'Sample Shopper';
        $user->email = 'shopper@onekilo.test';
        $user->phone = '+201140158807';
        $user->password = bcrypt('password');
        $user->gender = 'female';
        $user->country_id = $egyptId;
        $user->governorate_id = $cairoId;
        $user->region_id = $nasrCityId;
        $user->status = true;
        $user->birth_date = '1996-01-15';
        $user->email_verified_at = now();
        $user->save();


        $khalifa = User::query()->firstOrNew(['email' => 'khalifa@onekilo.test']);
        $khalifa->image = 'uploads/images/image.png';
        $khalifa->name = 'Khalifa Shopper';
        $khalifa->email = 'khalifa@onekilo.test';
        $khalifa->phone = '+201022113041';
        $khalifa->password = bcrypt('password');
        $khalifa->gender = 'male';
        $khalifa->country_id = $egyptId;
        $khalifa->governorate_id = $cairoId;
        $khalifa->region_id = $nasrCityId;
        $khalifa->status = true;
        $khalifa->birth_date = '1996-01-15';
        $khalifa->email_verified_at = now();
        $khalifa->save();
    }
}
