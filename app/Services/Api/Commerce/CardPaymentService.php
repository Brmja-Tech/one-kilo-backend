<?php

namespace App\Services\Api\Commerce;

use App\Exceptions\ApiBusinessException;
use App\Models\Order;
use App\Services\Payments\Kashier\KashierService;
use Illuminate\Support\Facades\Log;

class CardPaymentService
{
    public function __construct(protected KashierService $kashierService)
    {
    }

    public function generatePaymentUrl(Order $order): string
    {
        try {
            $payment = $this->kashierService->createCheckoutSession($order);

            return (string) $payment->payment_url;
        } catch (\Throwable $e) {
            Log::channel((string) config('kashier.log_channel', 'stack'))
                ->error('Card payment session creation failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'message' => $e->getMessage(),
                ]);

            throw new ApiBusinessException(
                __('front.payment-gateway-unavailable'),
                422,
                ['payment_method' => [__('front.payment-gateway-unavailable')]]
            );
        }
    }
}
