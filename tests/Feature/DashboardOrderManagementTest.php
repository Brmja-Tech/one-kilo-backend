<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class DashboardOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
        ]);
    }

    public function test_order_creation_creates_an_initial_status_log(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_PENDING, 'OK-INITIAL-1001');

        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $order->id,
            'old_status' => null,
            'new_status' => Order::STATUS_PENDING,
            'changed_by_admin_id' => null,
        ]);
    }

    public function test_admin_with_permission_can_update_order_status_and_log_it(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Admin::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_PENDING, 'OK-STATUS-1001');

        $response = $this->actingAs($admin, 'admin')
            ->post(route('dashboard.orders.status.update', $order), [
                'status' => Order::STATUS_CONFIRMED,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CONFIRMED,
        ]);

        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $order->id,
            'old_status' => Order::STATUS_PENDING,
            'new_status' => Order::STATUS_CONFIRMED,
            'changed_by_admin_id' => $admin->id,
        ]);
    }

    public function test_admin_without_permission_cannot_update_order_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = $this->createAdminWithPermissions(['orders']);
        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_PENDING, 'OK-STATUS-1002');

        $this->actingAs($admin, 'admin')
            ->post(route('dashboard.orders.status.update', $order), [
                'status' => Order::STATUS_CONFIRMED,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertDatabaseMissing('order_status_logs', [
            'order_id' => $order->id,
            'old_status' => Order::STATUS_PENDING,
            'new_status' => Order::STATUS_CONFIRMED,
        ]);
    }

    public function test_invalid_order_status_transition_is_rejected(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Admin::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_PENDING, 'OK-STATUS-1003');

        $response = $this->from(route('dashboard.orders.show', $order))
            ->actingAs($admin, 'admin')
            ->post(route('dashboard.orders.status.update', $order), [
                'status' => Order::STATUS_DELIVERED,
            ]);

        $response->assertRedirect(route('dashboard.orders.show', $order));
        $response->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertDatabaseMissing('order_status_logs', [
            'order_id' => $order->id,
            'old_status' => Order::STATUS_PENDING,
            'new_status' => Order::STATUS_DELIVERED,
        ]);
    }

    public function test_admin_with_orders_permission_can_view_printable_invoice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Admin::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_CONFIRMED, 'OK-PRINT-1001');

        $this->actingAs($admin, 'admin')
            ->get(route('dashboard.orders.print', $order))
            ->assertOk()
            ->assertSeeText('Invoice')
            ->assertSeeText($order->order_number)
            ->assertSeeText($user->name);
    }

    public function test_admin_without_orders_permission_cannot_view_printable_invoice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = $this->createAdminWithPermissions(['users']);
        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_CONFIRMED, 'OK-PRINT-1002');

        $this->actingAs($admin, 'admin')
            ->get(route('dashboard.orders.print', $order))
            ->assertForbidden();
    }

    public function test_user_profile_page_displays_wallet_orders_and_favorites(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Admin::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $order = $this->createOrder($user, Order::STATUS_CONFIRMED, 'OK-PROFILE-1001');

        $this->actingAs($admin, 'admin')
            ->get(route('dashboard.user.profile', ['id' => $user->id]))
            ->assertOk()
            ->assertSeeText($user->name)
            ->assertSeeText($order->order_number)
            ->assertSeeText('initial_bonus')
            ->assertSeeText('Mango Juice 1L');
    }

    public function test_user_profile_page_handles_empty_wallet_orders_and_favorites(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Admin::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $user = User::factory()->create([
            'name' => 'Empty Customer',
            'phone' => '+201199999999',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('dashboard.user.profile', ['id' => $user->id]))
            ->assertOk()
            ->assertSeeText('No wallet found for this user.')
            ->assertSeeText('No wallet transactions found.')
            ->assertSeeText('No orders found.')
            ->assertSeeText('No favorite products found.');
    }

    private function createAdminWithPermissions(array $permissions): Admin
    {
        $role = Role::query()->create([
            'role' => [
                'en' => 'Limited Admin',
                'ar' => 'مسؤول محدود',
            ],
            'permession' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
        ]);

        return Admin::query()->create([
            'image' => 'uploads/images/image.png',
            'name' => 'Limited Admin',
            'email' => 'limited-admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => true,
        ]);
    }

    private function createOrder(User $user, string $status, string $orderNumber): Order
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'status' => $status,
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 15,
            'total' => 115,
            'placed_at' => now(),
        ]);
    }
}
