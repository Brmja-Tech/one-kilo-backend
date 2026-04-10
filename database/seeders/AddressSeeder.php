<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'shopper@onekilo.test')->first();

        if (! $user) {
            return;
        }

        Address::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'label' => 'home',
            ],
            [
                'contact_name' => $user->name,
                'phone' => $user->phone,
                'country_id' => $user->country_id,
                'governorate_id' => $user->governorate_id,
                'region_id' => $user->region_id,
                'city' => 'Nasr City',
                'area' => 'Block 7',
                'street' => 'Makram Ebeid Street',
                'building_number' => '15',
                'floor' => '4',
                'apartment_number' => '12',
                'landmark' => 'Near City Stars',
                'latitude' => 30.0725000,
                'longitude' => 31.3465000,
                'is_default' => true,
                'status' => true,
            ]
        );
    }
}
