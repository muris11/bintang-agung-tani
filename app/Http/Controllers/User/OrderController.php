<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.riwayat', compact('orders'));
    }

    public function show(Order $order): View
    {
        // Check authorization - user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['items', 'statusHistories']);

        return view('user.detail-pesanan', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan pesanan ini.');
        }

        // Check if order can be cancelled
        if (! $order->canBeCancelled()) {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan. Status saat ini: '.$order->getStatusLabel());
        }

        try {
            $this->orderService->cancelOrder(
                $order,
                'Dibatalkan oleh pengguna',
                auth()->id()
            );

            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan: '.$e->getMessage());
        }
    }

    public function status(): RedirectResponse
    {
        // Redirect to orders index (consolidated view structure)
        return redirect()->route('user.orders.index');
    }
}
