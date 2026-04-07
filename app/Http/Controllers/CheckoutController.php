<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Checkout Controller - Placeholder for Task 14
 *
 * This controller handles the checkout process.
 * Full implementation will be added in the next task.
 */
class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request): RedirectResponse
    {
        // Placeholder - full implementation in Task 14
        // This will create an order from the cart

        return redirect()->route('user.orders.index')
            ->with('success', 'Pesanan berhasil dibuat!');
    }
}
