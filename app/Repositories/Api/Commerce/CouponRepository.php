<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Coupon;
use App\Models\CouponUsage;

class CouponRepository
{
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::query()
            ->where('code', $code)
            ->first();
    }

    public function userUsageCount(int $couponId, int $userId): int
    {
        return CouponUsage::query()
            ->where('coupon_id', $couponId)
            ->where('user_id', $userId)
            ->count();
    }

    public function lockById(int $couponId): ?Coupon
    {
        return Coupon::query()
            ->whereKey($couponId)
            ->lockForUpdate()
            ->first();
    }

    public function registerUsage(Coupon $coupon, int $userId, int $orderId, float $discountAmount): CouponUsage
    {
        $coupon->increment('used_count');

        return CouponUsage::query()->create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => round($discountAmount, 2),
            'used_at' => now(),
        ]);
    }
}
