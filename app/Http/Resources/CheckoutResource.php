<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $order = $this['order'];
        $wallet = $this['wallet'] ?? null;

        return [
            'order' => new OrderDetailsResource($order),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status,
            'payment_url' => $order->payment_url,
            'wallet' => $wallet ? new WalletResource($wallet) : null,
            'totals' => [
                'subtotal' => round((float) $order->subtotal, 2),
                'discount_amount' => round((float) $order->discount_amount, 2),
                'delivery_fee' => round((float) $order->delivery_fee, 2),
                'total' => round((float) $order->total, 2),
            ],
        ];
    }
}
