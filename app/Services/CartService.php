<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(int $userId): Cart
    {
        return Cart::getOrCreateForUser($userId);
    }

    public function addToCart(User $user, Product $product, int $quantity = 1, ?string $notes = null): CartItem
    {
        return DB::transaction(function () use ($user, $product, $quantity, $notes) {
            if (! $product->hasStock($quantity)) {
                throw new Exception("Product '{$product->name}' is out of stock. Only {$product->stock} item(s) available.");
            }

            if (! $product->isAvailableForOrder($quantity)) {
                throw new Exception($product->getAvailabilityMessage($quantity));
            }

            $cart = Cart::getOrCreateForUser($user->id);

            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $quantity;

                if (! $product->hasStock($newQuantity)) {
                    throw new Exception("Cannot add {$quantity} more item(s). Product '{$product->name}' only has {$product->stock} item(s) in stock. Current cart quantity: {$existingItem->quantity}");
                }

                if (! $product->isAvailableForOrder($newQuantity)) {
                    throw new Exception($product->getAvailabilityMessage($newQuantity));
                }

                $existingItem->updateQuantity($newQuantity);
                $cart->recalculateTotals();

                return $existingItem;
            }

            $unitPrice = $product->getCurrentPrice();
            $subtotal = $unitPrice * $quantity;

            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'notes' => $notes,
            ]);

            $cart->recalculateTotals();

            return $cartItem;
        });
    }

    public function updateQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cartItem, $quantity) {
            $product = $cartItem->product;
            $cart = $cartItem->cart;

            if ($quantity <= 0) {
                $cartItem->delete();
                $cart->recalculateTotals();

                return $cartItem;
            }

            if (! $product->hasStock($quantity)) {
                throw new Exception("Product '{$product->name}' is out of stock. Only {$product->stock} item(s) available.");
            }

            if (! $product->isAvailableForOrder($quantity)) {
                throw new Exception($product->getAvailabilityMessage($quantity));
            }

            $cartItem->updateQuantity($quantity);
            $cart->recalculateTotals();

            return $cartItem;
        });
    }

    public function removeItem(CartItem $cartItem): void
    {
        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart->recalculateTotals();
    }

    public function clearCart(int $userId): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->clear();
        }
    }

    public function getCartSummary(int $userId): array
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();

        if (! $cart || $cart->isEmpty()) {
            return [
                'items' => [],
                'total' => 0,
                'total_items' => 0,
                'is_empty' => true,
            ];
        }

        $items = $cart->items->map(function ($item) {
            $product = $item->product;
            $maxQuantity = min($product->stock, $product->max_order ?? PHP_INT_MAX);

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->getFirstImage(),
                'unit_price' => (float) $item->unit_price,
                'original_price' => $product->hasDiscount() ? (float) $product->price : null,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
                'max_quantity' => $maxQuantity,
                'notes' => $item->notes,
                'is_available' => $product->isAvailableForOrder($item->quantity),
            ];
        })->toArray();

        return [
            'items' => $items,
            'total' => $cart->getTotal(),
            'total_items' => $cart->getTotalItems(),
            'is_empty' => false,
        ];
    }

    public function validateForCheckout(int $userId): array
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();

        $errors = [];
        $warnings = [];

        if (! $cart || $cart->isEmpty()) {
            $errors[] = 'Your cart is empty. Please add items before checkout.';

            return [
                'valid' => false,
                'errors' => $errors,
                'warnings' => $warnings,
                'cart' => null,
            ];
        }

        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product->is_active) {
                $errors[] = "Product '{$product->name}' is no longer available.";
            }

            if (! $product->hasStock($item->quantity)) {
                $errors[] = "Product '{$product->name}' is out of stock. Only {$product->stock} item(s) available (you have {$item->quantity} in cart).";
            }

            if (! $product->isAvailableForOrder($item->quantity)) {
                $errors[] = $product->getAvailabilityMessage($item->quantity)." for '{$product->name}'.";
            }

            $currentPrice = $product->getCurrentPrice();
            if ((float) $item->unit_price !== (float) $currentPrice) {
                $warnings[] = "Price for '{$product->name}' has changed from Rp ".number_format($item->unit_price, 0, ',', '.').' to Rp '.number_format($currentPrice, 0, ',', '.').'.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'cart' => $cart,
        ];
    }

    public function syncPrices(int $userId): void
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();

        if (! $cart) {
            return;
        }

        foreach ($cart->items as $item) {
            $currentPrice = $item->product->getCurrentPrice();

            if ((float) $item->unit_price !== (float) $currentPrice) {
                $item->unit_price = $currentPrice;
                $item->subtotal = $currentPrice * $item->quantity;
                $item->save();
            }
        }

        $cart->recalculateTotals();
    }
}
