<?php

namespace Tests\Feature;

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
            ->assertJsonPath('data.0.children.0.name', 'Citrus')
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
            ->assertJsonPath('data.0.is_favorite', true)
            ->assertJsonPath('data.0.has_active_discount', true)
            ->assertJsonPath('data.0.price_before_discount', 42)
            ->assertJsonPath('data.0.price_after_discount', 37);
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
}
