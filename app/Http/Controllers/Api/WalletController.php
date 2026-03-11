<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Services\Api\Commerce\WalletService;
use Illuminate\Http\Request;

class WalletController extends ApiController
{
    public function __construct(protected WalletService $service)
    {
    }

    public function show()
    {
        $wallet = $this->service->current(auth('sanctum')->user()->id);

        return ApiResponse::sendResponse(
            200,
            __('front.wallet-retrieved-successfully'),
            new WalletResource($wallet)
        );
    }

    public function transactions(Request $request)
    {
        $transactions = $this->service->paginateTransactions(
            auth('sanctum')->user()->id,
            (int) $request->integer('per_page', 15)
        );

        return ApiResponse::sendResponse(
            200,
            __('front.wallet-transactions-retrieved-successfully'),
            WalletTransactionResource::collection($transactions),
            $this->paginationData($transactions)
        );
    }
}
