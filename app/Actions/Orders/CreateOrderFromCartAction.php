<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\StockService;
use Exception;
use Illuminate\Support\Facades\DB;

final class CreateOrderFromCartAction
{
    public function __construct(
        private CartService $cartService,
        private StockService $stockService
    ) {}

    /**
     * Create a new order from user's cart
     *
     * @throws Exception
     */
    public function handle(int $userId, array $orderData): Order
    {
        return DB::transaction(function () use ($userId, $orderData) {
            // Validate cart
            $validation = $this->cartService->validateForCheckout($userId);

            if (! $validation['valid']) {
                throw new Exception('Cart validation failed: '.implode(', ', $validation['errors']));
            }

            if ($validation['cart'] === null || $validation['cart']->isEmpty()) {
                throw new Exception('Cart is empty. Please add items before checkout.');
            }

            $cart = $validation['cart'];

            // Sync prices before creating order
            $this->cartService->syncPrices($userId);
            $cart->refresh();
            $cart->load('items.product');

            // Calculate totals
            $subtotal = $cart->getTotal();
            $shippingCost = $orderData['shipping_cost'] ?? 0;
            $total = $subtotal + $shippingCost;

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'status' => Order::STATUS_PAYMENT_PENDING,
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $total,
                'shipping_address' => $orderData['shipping_address'] ?? null,
                'shipping_phone' => $orderData['shipping_phone'] ?? null,
                'notes' => $orderData['notes'] ?? null,
                'shipping_courier' => $orderData['shipping_courier'] ?? null,
                'shipping_service' => $orderData['shipping_service'] ?? null,
            ]);

            // Create order items and deduct stock
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                $price = $product->getCurrentPrice();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->featured_image,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $price,
                    'subtotal' => $price * $cartItem->quantity,
                ]);

                // Deduct stock
                $this->stockService->deductStock(
                    $product,
                    $cartItem->quantity,
                    "Order #{$order->order_number}",
                    $userId
                );
            }

            // Clear cart
            $this->cartService->clearCart($userId);

            return $order->load('items');
        });
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'BAT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));

        return "{$prefix}-{$date}-{$random}";
    }
}
