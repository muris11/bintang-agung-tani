<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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
    $query = Order::with(['user']);

    // Filter by status
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // Filter by date range
    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Search by order number or user name
    if ($request->filled('search')) {
      $search = $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
      ])['search'] ?? '';

      if (! empty($search)) {
        $query->where(function ($q) use ($search) {
          $q->where('order_number', 'like', "%{$search}%")
            ->orWhereHas('user', function ($userQuery) use ($search) {
              $userQuery->where('name', 'like', "%{$search}%");
            });
        });
      }
    }

    $orders = $query->orderBy('created_at', 'desc')
      ->paginate(15)
      ->withQueryString();

    // Get all status options for filter dropdown
    $statuses = Order::STATUS_LABELS;

    // Calculate statistics for dashboard cards - database agnostic approach
    $now = now();
    $startOfMonth = $now->copy()->startOfMonth();
    $endOfMonth = $now->copy()->endOfMonth();

    $statsResult = Order::selectRaw(
      'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as verification,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN status = ? AND created_at >= ? AND created_at <= ? THEN 1 ELSE 0 END) as completed_this_month',
      [
        Order::STATUS_MENUNGGU_VERIFIKASI,
        Order::STATUS_PROCESSING,
        Order::STATUS_CANCELLED,
        Order::STATUS_COMPLETED,
        $startOfMonth,
        $endOfMonth,
      ]
    )->first();

    $stats = [
      'verification' => (int) $statsResult->verification,
      'processing' => (int) $statsResult->processing,
      'cancelled' => (int) $statsResult->cancelled,
      'completed_this_month' => (int) $statsResult->completed_this_month,
    ];

    return view('admin.pesanan', compact('orders', 'statuses', 'stats'));
  }

  public function show(Order $order): View
  {
    $order->load(['items', 'statusHistories', 'user', 'address', 'activityLogs.user']);

    return view('admin.detail-pesanan', compact('order'));
  }

  public function cancel(Request $request, Order $order): RedirectResponse
  {
    $request->validate([
      'reason' => 'nullable|string|max:500',
    ]);

    // Check if order can be cancelled
    if (! $order->canBeCancelled()) {
      return redirect()->back()->with('error', 'Pesanan ini tidak dapat dibatalkan karena status sudah tidak valid.');
    }

    $reason = $request->reason ?? 'Dibatalkan oleh admin';
    $previousStatus = $order->status;

    try {
      // Update order status to cancelled
      $order->updateStatus(
        Order::STATUS_CANCELLED,
        $reason,
        auth()->id()
      );

      // Dispatch event for notifications
      event(new \App\Events\OrderCancelled($order->fresh(), $reason, auth()->id()));

      ActivityLog::log(
        'order_cancelled',
        Order::class,
        $order->id,
        "Pesanan {$order->order_number} dibatalkan oleh admin. Alasan: {$reason}",
        ['previous_status' => $previousStatus, 'reason' => $reason]
      );

      return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
    } catch (\Exception $e) {
      ActivityLog::log(
        'order_cancel_failed',
        Order::class,
        $order->id,
        "Gagal membatalkan pesanan {$order->order_number}: {$e->getMessage()}",
        ['error' => $e->getMessage()]
      );

      return redirect()->back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
    }
  }

  public function updateStatus(Request $request, Order $order): RedirectResponse
  {
    $request->validate([
      'status' => 'required|string',
      'notes' => 'nullable|string|max:500',
    ]);

    $newStatus = $request->status;
    $notes = $request->notes;
    $previousStatus = $order->status;

    // Get valid transitions
    $validTransitions = $this->orderService->getValidStatusTransitions($order->status);

    // Validate status transition
    if (! in_array($newStatus, $validTransitions)) {
      return redirect()->back()->with('error', 'Transisi status tidak valid. Transisi yang valid: ' . implode(', ', $validTransitions));
    }

    try {
      $this->orderService->updateStatus(
        $order,
        $newStatus,
        $notes,
        auth()->id()
      );

      // Dispatch event for notifications and real-time updates
      event(new \App\Events\OrderStatusChanged($order->fresh(), $previousStatus, $notes, auth()->id()));

      ActivityLog::log(
        'order_status_updated',
        Order::class,
        $order->id,
        "Status pesanan {$order->order_number} diubah dari {$previousStatus} ke {$newStatus}",
        ['previous_status' => $previousStatus, 'new_status' => $newStatus]
      );

      return redirect()->back()->with('success', "Status pesanan berhasil diperbarui menjadi: {$order->fresh()->getStatusLabel()}");
    } catch (\Exception $e) {
      ActivityLog::log(
        'order_status_update_failed',
        Order::class,
        $order->id,
        "Gagal mengubah status pesanan {$order->order_number}: {$e->getMessage()}",
        ['error' => $e->getMessage()]
      );

      return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
    }
  }

  /**
   * Display page for bulk status update
   */
  public function bulkUpdateStatusPage(Request $request): View
  {
    $query = Order::with(['user'])->orderBy('created_at', 'desc');

    // Filter by status
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // Filter by date range
    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Search by order number or user name
    if ($request->filled('search')) {
      $search = $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
      ])['search'] ?? '';

      if (! empty($search)) {
        $query->where(function ($q) use ($search) {
          $q->where('order_number', 'like', "%{$search}%")
            ->orWhereHas('user', function ($userQuery) use ($search) {
              $userQuery->where('name', 'like', "%{$search}%");
            });
        });
      }
    }

    $orders = $query->paginate(50)->withQueryString();

    return view('admin.update-status', compact('orders'));
  }

  /**
   * Handle bulk status update
   */
  public function bulkUpdateStatus(Request $request): RedirectResponse
  {
    $request->validate([
      'order_ids' => 'required|array|min:1',
      'order_ids.*' => 'exists:orders,id',
      'status' => 'required|string|in:pending,menunggu_verifikasi,processing,completed,cancelled',
    ], [
      'order_ids.required' => 'Pilih minimal 1 pesanan.',
      'order_ids.min' => 'Pilih minimal 1 pesanan.',
      'status.required' => 'Pilih status baru.',
    ]);

    $orderIds = $request->order_ids;
    $newStatus = $request->status;
    $updatedCount = 0;
    $failedCount = 0;

    foreach ($orderIds as $orderId) {
      $order = Order::find($orderId);

      if (! $order) {
        $failedCount++;

        continue;
      }

      // Get valid transitions
      $validTransitions = $this->orderService->getValidStatusTransitions($order->status);

      // Skip if transition not valid
      if (! in_array($newStatus, $validTransitions)) {
        $failedCount++;

        continue;
      }

      try {
        $this->orderService->updateStatus(
          $order,
          $newStatus,
          'Update massal via halaman Update Status',
          auth()->id()
        );
        $updatedCount++;
      } catch (\Exception $e) {
        $failedCount++;
      }
    }

    $message = "Berhasil update {$updatedCount} pesanan.";
    if ($failedCount > 0) {
      $message .= " {$failedCount} pesanan gagal (transisi status tidak valid).";
    }

    ActivityLog::log(
      'bulk_order_status_updated',
      Order::class,
      null,
      "Update massal status pesanan: {$updatedCount} berhasil, {$failedCount} gagal",
      ['updated_count' => $updatedCount, 'failed_count' => $failedCount, 'new_status' => $newStatus]
    );

    return redirect()->route('admin.orders.bulk-update-status.page')
      ->with('success', $message);
  }

  public function addTracking(Request $request, Order $order): RedirectResponse
  {
    $request->validate([
      'courier' => 'required|string|max:50',
      'tracking_number' => 'required|string|max:100',
    ], [
      'courier.required' => 'Nama kurir harus diisi.',
      'courier.max' => 'Nama kurir maksimal 50 karakter.',
      'tracking_number.required' => 'Nomor resi harus diisi.',
      'tracking_number.max' => 'Nomor resi maksimal 100 karakter.',
    ]);

    try {
      $this->orderService->addTracking(
        $order,
        $request->courier,
        $request->tracking_number
      );

      ActivityLog::log(
        'tracking_added',
        Order::class,
        $order->id,
        "Nomor resi ditambahkan untuk pesanan {$order->order_number}: {$request->tracking_number} ({$request->courier})",
        ['tracking_number' => $request->tracking_number, 'courier' => $request->courier]
      );

      return redirect()->back()->with('success', 'Nomor resi pengiriman berhasil ditambahkan.');
    } catch (\Exception $e) {
      ActivityLog::log(
        'tracking_add_failed',
        Order::class,
        $order->id,
        "Gagal menambahkan nomor resi untuk pesanan {$order->order_number}: {$e->getMessage()}",
        ['error' => $e->getMessage()]
      );

      return redirect()->back()->with('error', 'Gagal menambahkan nomor resi: ' . $e->getMessage());
    }
  }

  public function stats(): View
  {
    $stats = $this->orderService->getOrderStats();

    return view('admin.dashboard', compact('stats'));
  }
}
