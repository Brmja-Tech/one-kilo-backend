<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'shopper@onekilo.test')->first();

        if (! $user) {
            return;
        }

        $Wallet = Wallet::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 500,
                'status' => true,
            ]
        );

        $Wallet->transactions()->create([
            'user_id' => $user->id,
            'type' => WalletTransaction::TYPE_CREDIT,
            'transaction_type' => WalletTransaction::REASON_BONUS,
            'amount' => 500,
            'balance_before' => 0,
            'balance_after' => 500,
            'reference' => 'initial_bonus',
            'notes' => 'Initial bonus for new user',
            'status' => true,
        ]);
    }
}
