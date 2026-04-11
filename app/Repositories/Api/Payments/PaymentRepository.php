<?php

namespace App\Repositories\Api\Payments;

use App\Models\Payment;

class PaymentRepository
{
    public function create(array $data): Payment
    {
        return Payment::query()->create($data);
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);

        return $payment;
    }

    public function findById(int $id): ?Payment
    {
        return Payment::query()->whereKey($id)->first();
    }

    public function findBySessionId(string $sessionId): ?Payment
    {
        return Payment::query()
            ->where('session_id', $sessionId)
            ->latest('id')
            ->first();
    }

    public function findByMerchantOrderId(string $merchantOrderId): ?Payment
    {
        return Payment::query()
            ->where('merchant_order_id', $merchantOrderId)
            ->latest('id')
            ->first();
    }
}

