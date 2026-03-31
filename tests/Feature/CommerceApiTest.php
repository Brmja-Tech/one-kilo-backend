<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommerceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_endpoint_returns_root_tree(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->getJson('/api/categories');

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.0.name', 'Fruits')
            ->assertJsonPath('data.0.slug', 'fruits')
            ->assertJsonPath('data.0.color', '0xFFE4572E')
            ->assertJsonPath('data.0.children.0.name', 'Citrus')
            ->assertJsonPath('data.0.children.0.parent_slug', 'fruits')
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonMissingPath('data.0.parent_id')
            ->assertJsonStructure([
                'code',
                'message',
                'data',
                'pagination' => ['total', 'current_page', 'last_page', 'per_page'],
            ]);
    }

    public function test_products_endpoint_returns_discount_pricing_and_favorite_state(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/products?category_slug=beverages&has_discount=1&sort=price_desc');

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.0.name', 'Mango Juice 1L')
            ->assertJsonPath('data.0.slug', 'mango-juice-1l')
            ->assertJsonPath('data.0.is_favorite', true)
            ->assertJsonPath('data.0.has_active_discount', true)
            ->assertJsonPath('data.0.price_before_discount', 42)
            ->assertJsonPath('data.0.price_after_discount', 37)
            ->assertJsonPath('data.0.category.slug', 'juice')
            ->assertJsonPath('data.0.category.color', '0xFFFF9800')
            ->assertJsonPath('data.0.category.parent_slug', 'beverages')
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonMissingPath('data.0.category.id');
    }

    public function test_best_selling_products_endpoint_returns_paginated_sales_ranking(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        $address = Address::query()->where('user_id', $user->id)->firstOrFail();
        $mangoJuice = Product::query()->where('slug', 'mango-juice-1l')->firstOrFail();
        $chips = Product::query()->where('slug', 'sea-salt-chips')->firstOrFail();
        $cola = Product::query()->where('slug', 'cola-can-330ml')->firstOrFail();
        $avocado = Product::query()->where('slug', 'imported-avocado')->firstOrFail();

        $confirmedOrder = $this->createOrder($user, $address, 'OK-BEST-SELLING-1', Order::STATUS_CONFIRMED, Order::PAYMENT_STATUS_PAID);
        $confirmedOrder->items()->create([
            'product_id' => $mangoJuice->id,
            'product_name' => $mangoJuice->name,
            'product_image' => $mangoJuice->image,
            'unit_price' => $mangoJuice->priceAfterDiscount(),
            'quantity' => 5,
            'line_total' => round($mangoJuice->priceAfterDiscount() * 5, 2),
        ]);

        $preparingOrder = $this->createOrder($user, $address, 'OK-BEST-SELLING-2', Order::STATUS_PREPARING, Order::PAYMENT_STATUS_PAID);
        $preparingOrder->items()->create([
            'product_id' => $chips->id,
            'product_name' => $chips->name,
            'product_image' => $chips->image,
            'unit_price' => $chips->priceAfterDiscount(),
            'quantity' => 3,
            'line_total' => round($chips->priceAfterDiscount() * 3, 2),
        ]);

        $pendingOrder = $this->createOrder($user, $address, 'OK-BEST-SELLING-3', Order::STATUS_PENDING, Order::PAYMENT_STATUS_UNPAID);
        $pendingOrder->items()->create([
            'product_id' => $cola->id,
            'product_name' => $cola->name,
            'product_image' => $cola->image,
            'unit_price' => $cola->priceAfterDiscount(),
            'quantity' => 50,
            'line_total' => round($cola->priceAfterDiscount() * 50, 2),
        ]);

        $canceledOrder = $this->createOrder($user, $address, 'OK-BEST-SELLING-4', Order::STATUS_CANCELED, Order::PAYMENT_STATUS_REFUNDED);
        $canceledOrder->items()->create([
            'product_id' => $avocado->id,
            'product_name' => $avocado->name,
            'product_image' => $avocado->image,
            'unit_price' => $avocado->priceAfterDiscount(),
            'quantity' => 40,
            'line_total' => round($avocado->priceAfterDiscount() * 40, 2),
        ]);

        $response = $this->getJson('/api/products/best-selling?per_page=1');

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'mango-juice-1l')
            ->assertJsonPath('data.0.sold_quantity', 5)
            ->assertJsonPath('data.0.category.slug', 'juice')
            ->assertJsonPath('data.0.category.color', '0xFFFF9800')
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonPath('pagination.per_page', 1)
            ->assertJsonPath('pagination.current_page', 1);
    }

    public function test_category_show_endpoint_uses_slug_and_returns_color(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->getJson('/api/categories/beverages');

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.slug', 'beverages')
            ->assertJsonPath('data.color', '0xFF1946B9')
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.parent_id');
    }

    public function test_category_products_and_product_show_endpoints_use_slugs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $categoryProductsResponse = $this->getJson('/api/categories/juice/products');

        $categoryProductsResponse
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.0.slug', 'mango-juice-1l')
            ->assertJsonPath('data.0.category.slug', 'juice');

        $productResponse = $this->getJson('/api/products/mango-juice-1l');

        $productResponse
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.slug', 'mango-juice-1l')
            ->assertJsonPath('data.category.slug', 'juice')
            ->assertJsonPath('data.category.color', '0xFFFF9800')
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.category.id');
    }

    public function test_category_and_product_filters_reject_legacy_numeric_parameters(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->getJson('/api/categories?parent_id=1')
            ->assertUnprocessable()
            ->assertJsonStructure(['data' => ['parent_id']]);

        $this->getJson('/api/products?category_id=1')
            ->assertUnprocessable()
            ->assertJsonStructure(['data' => ['category_id']]);
    }

    public function test_category_listing_accepts_parent_slug_filter(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->getJson('/api/categories?parent_slug=fruits');

        $response
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.parent_slug', 'fruits')
            ->assertJsonPath('data.1.parent_slug', 'fruits');
    }

    public function test_cart_endpoints_cover_add_update_coupon_remove_and_clear(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'shopper@onekilo.test')->firstOrFail();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/cart/clear')
            ->assertOk()
            ->assertJsonPath('data.items_count', 0)
            ->assertJsonPath('data.total', 0);

        $addResponse = $this->postJson('/api/cart/add', [
            'product_slug' => 'mango-juice-1l',
            'quantity' => 2,
        ]);

        $addResponse
            ->assertOk()
            ->assertJsonPath('data.items_count', 2)
            ->assertJsonPath('data.subtotal', 74);

        $itemId = $addResponse->json('data.items.0.id');

        $this->putJson('/api/cart/items/' . $itemId, [
            'quantity' => 3,
        ])
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 3)
            ->assertJsonPath('data.subtotal', 111);

        $this->postJson('/api/cart/apply-coupon', [
            'code' => 'WELCOME10',
        ])
            ->assertOk()
            ->assertJsonPath('data.coupon.code', 'WELCOME10')
            ->assertJsonPath('data.discount_total', 11.1)
            ->assertJsonPath('data.total', 99.9);

        $this->deleteJson('/api/cart/items/' . $itemId)
            ->assertOk()
            ->assertJsonPath('data.items_count', 0)
            ->assertJsonPath('data.coupon_id', null);

        $this->deleteJson('/api/cart/clear')
            ->assertOk()
            ->assertJsonPath('data.items_count', 0)
            ->assertJsonPath('data.total', 0);
    }

    private function createOrder(User $user, Address $address, string $orderNumber, string $status, string $paymentStatus): Order
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_number' => $orderNumber,
            'status' => $status,
            'payment_method' => Order::PAYMENT_METHOD_WALLET,
            'payment_status' => $paymentStatus,
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 100,
            'placed_at' => now(),
            'paid_at' => $paymentStatus === Order::PAYMENT_STATUS_PAID ? now() : null,
        ]);
    }
}