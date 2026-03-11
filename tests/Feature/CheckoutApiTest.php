<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_endpoints_manage_default_addresses(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        Sanctum::actingAs($user);

        $this->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.0.label', 'home')
            ->assertJsonPath('data.0.is_default', true);

        $createResponse = $this->postJson('/api/addresses', [
            'label' => 'work',
            'contact_name' => 'Office Reception',
            'phone' => '201122233344',
            'country_id' => $user->country_id,
            'governorate_id' => $user->governorate_id,
            'city' => 'Cairo',
            'area' => 'New Cairo',
            'street' => '90 Street',
            'building_number' => '22B',
            'floor' => '6',
            'apartment_number' => '601',
            'landmark' => 'Near business park',
            'is_default' => true,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.label', 'work')
            ->assertJsonPath('data.is_default', true);

        $workAddressId = $createResponse->json('data.id');

        $this->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.label', 'work')
            ->assertJsonPath('data.1.is_default', false);

        $this->deleteJson('/api/addresses/' . $workAddressId)
            ->assertOk();

        $this->getJson('/api/addresses')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.label', 'home')
            ->assertJsonPath('data.0.is_default', true);
    }

    public function test_wallet_checkout_creates_paid_order_wallet_transaction_and_clears_cart(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $addressId = Address::query()->where('user_id', $user->id)->value('id');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout', [
            'address_id' => $addressId,
            'payment_method' => Order::PAYMENT_METHOD_WALLET,
            'notes' => 'Leave the order with the guard.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.payment_method', Order::PAYMENT_METHOD_WALLET)
            ->assertJsonPath('data.payment_status', Order::PAYMENT_STATUS_PAID)
            ->assertJsonPath('data.order_status', Order::STATUS_CONFIRMED)
            ->assertJsonPath('data.payment_url', null)
            ->assertJsonPath('data.totals.subtotal', 120)
            ->assertJsonPath('data.totals.discount_amount', 12)
            ->assertJsonPath('data.totals.delivery_fee', 35)
            ->assertJsonPath('data.totals.total', 143)
            ->assertJsonPath('data.wallet.balance', 357);

        $order = Order::query()->firstOrFail();
        $wallet = Wallet::query()->where('user_id', $user->id)->firstOrFail();
        $coupon = Coupon::query()->where('code', 'WELCOME10')->firstOrFail();

        $this->assertSame(Order::PAYMENT_METHOD_WALLET, $order->payment_method);
        $this->assertSame(Order::PAYMENT_STATUS_PAID, $order->payment_status);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
        $this->assertNotNull($order->wallet_transaction_id);
        $this->assertSame('357.00', $wallet->balance);
        $this->assertDatabaseCount('order_items', 3);
        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertDatabaseCount('coupon_usages', 1);
        $this->assertSame(1, $coupon->fresh()->used_count);
        $this->assertSame(0, Cart::query()->where('user_id', $user->id)->firstOrFail()->items()->count());

        $this->getJson('/api/wallet/transactions')
            ->assertOk()
            ->assertJsonPath('data.0.type', WalletTransaction::TYPE_DEBIT)
            ->assertJsonPath('data.0.order.order_number', $order->order_number);
    }

    public function test_card_checkout_returns_payment_url_without_wallet_debit_or_coupon_usage(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $addressId = Address::query()->where('user_id', $user->id)->value('id');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout', [
            'address_id' => $addressId,
            'payment_method' => Order::PAYMENT_METHOD_CARD,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.payment_method', Order::PAYMENT_METHOD_CARD)
            ->assertJsonPath('data.payment_status', Order::PAYMENT_STATUS_PENDING)
            ->assertJsonPath('data.order_status', Order::STATUS_AWAITING_PAYMENT)
            ->assertJsonPath('data.wallet', null);

        $paymentUrl = $response->json('data.payment_url');

        $this->assertNotNull($paymentUrl);
        $this->assertStringStartsWith('https://example.com/pay/OK-', $paymentUrl);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('wallet_transactions', 0);
        $this->assertDatabaseCount('coupon_usages', 0);
        $this->assertSame(0, Coupon::query()->where('code', 'WELCOME10')->firstOrFail()->used_count);
        $this->assertSame('500.00', Wallet::query()->where('user_id', $user->id)->firstOrFail()->balance);
        $this->assertSame(0, Cart::query()->where('user_id', $user->id)->firstOrFail()->items()->count());
    }
}
