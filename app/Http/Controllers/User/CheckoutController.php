<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cartService;

    protected $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function index()
    {
        $user = Auth::user();
        $cart = $this->cartService->getOrCreateCart($user->id);

        if ($cart->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Your cart is empty.');
        }

        $cart->load('items.product');
        $addresses = $user->addresses()->get();

        return view('user.checkout', [
            'cart' => $cart,
            'addresses' => $addresses,
            'subtotal' => $cart->getTotal(),
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        $user = Auth::user();

        // Validate cart
        $validation = $this->cartService->validateForCheckout($user->id);

        if (! $validation['valid']) {
            return redirect()->back()->with('error', 'Cart validation failed: '.implode(', ', $validation['errors']));
        }

        if ($validation['cart'] === null || $validation['cart']->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        try {
            // Prepare order data
            $orderData = [
                'address_id' => $request->input('address_id'),
                'shipping_cost' => $request->input('shipping_cost', 0),
                'shipping_courier' => $request->input('shipping_courier'),
                'shipping_service' => $request->input('shipping_service'),
                'notes' => $request->input('notes'),
            ];

            // If no address_id provided, use the shipping_address and shipping_phone from request
            if (! $request->input('address_id')) {
                $orderData['shipping_address'] = $request->input('shipping_address');
                $orderData['shipping_phone'] = $request->input('shipping_phone');
            }

            // Create order from cart
            $order = $this->orderService->createFromCart($user, $orderData);

            // Redirect to manual payment method selection (Midtrans removed)
            return redirect()->route('user.payments.select-method', $order)
                ->with('success', 'Pesanan berhasil dibuat. Silakan pilih metode pembayaran.');
        } catch (\Exception $e) {
            // Log detailed error for debugging, show generic message to user
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'request_data' => $request->except(['password', 'token']),
            ]);

            return redirect()->back()->with('error', 'Gagal membuat pesanan. Silakan coba lagi atau hubungi customer service.');
        }
    }
}
