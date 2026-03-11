<?php

namespace App\Services\Api\Commerce;

use App\Models\Order;

class CardPaymentService
{
    public function generatePaymentUrl(Order $order): string
    {
        return 'https://example.com/pay/' . $order->order_number;
    }
}
