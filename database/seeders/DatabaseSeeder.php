<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EgyptLocationSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            SettingsSeeder::class,
            BannerSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
            WalletSeeder::class,
            CategorySeeder::class,
            VariantSeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            CouponSeeder::class,
            FavoriteSeeder::class,
            CartSeeder::class,
            WorkingHoursSeeder::class,
            DeliveryStatusSeeder::class,
        ]);
    }
}
