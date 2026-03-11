<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WalletRepository
{
    public function getOrCreateForUser(int $userId): Wallet
    {
        $wallet = Wallet::query()->firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'status' => true]
        );

        return $this->loadDetails($wallet);
    }

    public function findForUser(int $userId, bool $lockForUpdate = false): ?Wallet
    {
        $query = Wallet::query()->where('user_id', $userId);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public function loadDetails(Wallet $wallet, int $transactionsLimit = 5): Wallet
    {
        return $wallet->load([
            'transactions' => fn ($query) => $query
                ->with('order')
                ->latest('id')
                ->limit($transactionsLimit),
        ]);
    }

    public function updateBalance(Wallet $wallet, float $newBalance): Wallet
    {
        $wallet->update([
            'balance' => round($newBalance, 2),
        ]);

        return $wallet;
    }

    public function createTransaction(array $data): WalletTransaction
    {
        return WalletTransaction::query()->create($data);
    }

    public function paginateTransactions(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return WalletTransaction::query()
            ->where('user_id', $userId)
            ->with('order')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
