<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

final class UpdateOrderStatusAction
{
    public function __construct(
        private StockService $stockService
    ) {}

    /**
     * Update order status with validation and history tracking
     */
    public function handle(
        Order $order,
        string $newStatus,
        ?string $notes = null,
        ?int $changedBy = null
    ): Order {
        return DB::transaction(function () use ($order, $newStatus, $notes, $changedBy) {
            $previousStatus = $order->status;

            // Update order status
            $order->update(['status' => $newStatus]);

            // Create status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => $previousStatus,
                'status' => $newStatus,
                'notes' => $notes,
                'changed_by' => $changedBy,
            ]);

            // Handle stock restoration for cancelled orders
            if ($newStatus === Order::STATUS_CANCELLED) {
                $this->restoreStockForCancelledOrder($order);
            }

            return $order->fresh();
        });
    }

    /**
     * Restore stock when order is cancelled
     */
    private function restoreStockForCancelledOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            $this->stockService->increaseStock(
                $item->product,
                $item->quantity,
                "Order #{$order->order_number} cancelled - stock restored"
            );
        }
    }
}
