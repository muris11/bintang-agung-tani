<?php

namespace App\Actions\Cart;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderQuantityException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

final class AddToCartAction
{
    /**
     * Add product to user's cart
     */
    public function handle(int $userId, Product $product, int $quantity, ?string $notes = null): CartItem
    {
        return DB::transaction(function () use ($userId, $product, $quantity, $notes) {
            $cart = Cart::getOrCreateForUser($userId);

            // Check if product already in cart
            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->first();

            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem->quantity + $quantity;
                $this->validateStock($product, $newQuantity);

                $existingItem->update([
                    'quantity' => $newQuantity,
                    'subtotal' => $product->getCurrentPrice() * $newQuantity,
                ]);

                $cart->recalculateTotals();

                return $existingItem->fresh();
            }

            // Validate stock for new item
            $this->validateStock($product, $quantity);

            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->getCurrentPrice(),
                'subtotal' => $product->getCurrentPrice() * $quantity,
                'notes' => $notes,
            ]);

            $cart->recalculateTotals();

            return $cartItem;
        });
    }

    /**
     * Validate stock availability
     *
     * @throws InsufficientStockException
     * @throws InvalidOrderQuantityException
     */
    private function validateStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product, $quantity);
        }

        if ($product->min_order && $quantity < $product->min_order) {
            throw new InvalidOrderQuantityException($product, $quantity);
        }

        if ($product->max_order && $quantity > $product->max_order) {
            throw new InvalidOrderQuantityException($product, $quantity);
        }
    }
}
