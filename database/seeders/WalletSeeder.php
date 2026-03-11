<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'shopper@onekilo.test')->first();

        if (! $user) {
            return;
        }

        Wallet::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 500,
                'status' => true,
            ]
        );
    }
}
