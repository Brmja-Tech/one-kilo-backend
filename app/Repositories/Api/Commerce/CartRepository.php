<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartRepository
{
    public function getOrCreateForUser(int $userId): Cart
    {
        $cart = Cart::query()->firstOrCreate(['user_id' => $userId]);

        return $this->loadDetails($cart);
    }

    public function findForUser(int $userId): ?Cart
    {
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->first();

        return $cart ? $this->loadDetails($cart) : null;
    }

    public function loadDetails(Cart $cart): Cart
    {
        return $cart->load([
            'coupon',
            'items' => fn ($query) => $query->orderBy('id'),
            'items.product' => fn ($query) => $query->with('category.parent:id,slug'),
        ]);
    }

    public function findUserItem(int $userId, int $itemId): CartItem
    {
        return CartItem::query()
            ->whereKey($itemId)
            ->whereHas('cart', fn ($query) => $query->where('user_id', $userId))
            ->with(['product.category.parent:id,slug', 'cart.coupon'])
            ->firstOrFail();
    }

    public function addOrIncrementItem(Cart $cart, Product $product, int $quantity): CartItem
    {
        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = $item->exists
            ? $item->quantity + $quantity
            : $quantity;

        $item->save();

        return $item;
    }

    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
    }

    public function setCoupon(Cart $cart, ?int $couponId): Cart
    {
        $cart->update(['coupon_id' => $couponId]);

        return $cart;
    }
}
