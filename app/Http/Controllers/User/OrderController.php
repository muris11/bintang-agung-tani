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
        $userId = auth()->id();
        $query = Order::where('user_id', $userId)
            ->with(['items.product']);

        $status = $request->query('status', 'semua');

        if ($status && $status !== 'semua') {
            $statusMapping = [
                'menunggu-verifikasi' => Order::STATUS_MENUNGGU_VERIFIKASI,
                'diproses' => Order::STATUS_PROCESSING,
                'selesai' => Order::STATUS_COMPLETED,
                'dibatalkan' => Order::STATUS_CANCELLED,
            ];

            $dbStatus = $statusMapping[$status] ?? $status;
            $query->where('status', $dbStatus);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.riwayat', compact('orders'));
    }

    public function show(Order $order): View
    {
        // Use policy for authorization
        $this->authorize('view', $order);

        $order->load(['items.product', 'statusHistories', 'user', 'paymentMethod']);

        return view('user.detail-pesanan', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        // Use policy for authorization
        $this->authorize('cancel', $order);

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
