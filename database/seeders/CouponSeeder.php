<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 100,
                'max_discount_amount' => 50,
                'usage_limit' => 500,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'starts_at' => now()->subDays(30),
                'expires_at' => now()->addDays(60),
                'status' => true,
            ],
            [
                'code' => 'SAVE20',
                'type' => 'amount',
                'value' => 20,
                'min_order_amount' => 150,
                'max_discount_amount' => null,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 5,
                'used_count' => 0,
                'starts_at' => now()->subDays(15),
                'expires_at' => now()->addDays(45),
                'status' => true,
            ],
            [
                'code' => 'WEEKEND15',
                'type' => 'percentage',
                'value' => 15,
                'min_order_amount' => 200,
                'max_discount_amount' => 40,
                'usage_limit' => 300,
                'usage_limit_per_user' => 2,
                'used_count' => 0,
                'starts_at' => now()->subDays(1),
                'expires_at' => now()->addDays(14),
                'status' => true,
            ],
            [
                'code' => 'OLDDEAL',
                'type' => 'amount',
                'value' => 25,
                'min_order_amount' => 120,
                'max_discount_amount' => null,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'starts_at' => now()->subDays(60),
                'expires_at' => now()->subDays(1),
                'status' => false,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::query()->updateOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }
    }
}
