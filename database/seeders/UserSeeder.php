<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $egypt = Country::query()
            ->get()
            ->first(fn(Country $country) => $country->getTranslation('name', 'en') === 'Egypt');

        $cairo = Governorate::query()
            ->where('country_id', $egypt?->id)
            ->get()
            ->first(fn(Governorate $governorate) => $governorate->getTranslation('name', 'en') === 'Cairo');

        $user = User::query()->firstOrNew(['email' => 'shopper@onekilo.test']);
        $user->image = 'uploads/images/image.png';
        $user->name = 'Sample Shopper';
        $user->email = 'shopper@onekilo.test';
        $user->phone = '+201140158807';
        $user->password = bcrypt('password');
        $user->gender = 'female';
        $user->country_id = $egypt?->id;
        $user->governorate_id = $cairo?->id;
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
        $khalifa->country_id = $egypt?->id;
        $khalifa->governorate_id = $cairo?->id;
        $khalifa->status = true;
        $khalifa->birth_date = '1996-01-15';
        $khalifa->email_verified_at = now();
        $khalifa->save();
    }
}
