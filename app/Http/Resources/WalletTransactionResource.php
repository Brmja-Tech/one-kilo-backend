<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'transaction_type' => $this->transaction_type,
            'amount' => round((float) $this->amount, 2),
            'balance_before' => round((float) $this->balance_before, 2),
            'balance_after' => round((float) $this->balance_after, 2),
            'reference' => $this->reference,
            'notes' => $this->notes,
            'status' => (bool) $this->status,
            'order' => $this->whenLoaded('order', fn () => $this->order ? [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->order->status,
                'total' => round((float) $this->order->total, 2),
            ] : null),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
