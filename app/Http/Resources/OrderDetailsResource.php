<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderDetailsResource extends OrderResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'wallet_transaction' => $this->whenLoaded('walletTransaction', fn () => $this->walletTransaction
                ? new WalletTransactionResource($this->walletTransaction)
                : null),
        ];
    }
}
