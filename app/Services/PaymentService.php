<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createPayment(Order $order, string $paymentMethod): Payment
    {
        $existingPayment = Payment::where('order_id', $order->id)
            ->where('status', Payment::STATUS_PENDING)
            ->first();

        if ($existingPayment) {
            return $existingPayment;
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_method' => $paymentMethod,
            'provider' => 'manual',
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_PENDING,
            'expired_at' => now()->addHours(24),
        ]);

        $order->status = Order::STATUS_PENDING;
        $order->save();

        return $payment;
    }

    public function processManualPayment(Order $order, array $paymentData, int $processedBy): Payment
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_method' => $paymentData['payment_method'] ?? 'manual',
            'provider' => 'manual',
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_SUCCESS,
            'paid_at' => now(),
            'notes' => $paymentData['notes'] ?? null,
            'payment_data' => $paymentData,
        ]);

        $this->orderService->processPayment(
            $order,
            $payment->amount,
            $payment->payment_method,
            [
                'processed_by' => $processedBy,
                'paid_at' => now(),
            ],
            $processedBy
        );

        return $payment;
    }

    public function getPaymentByOrder(Order $order): ?Payment
    {
        return Payment::where('order_id', $order->id)
            ->latest()
            ->first();
    }

    public function refundPayment(Payment $payment, float $amount, string $reason): bool
    {
        if (! $payment->isSuccess()) {
            throw new \Exception('Cannot refund payment that is not successful');
        }

        if ($payment->status === Payment::STATUS_REFUNDED) {
            throw new \Exception('Payment has already been refunded');
        }

        if ($amount > $payment->amount) {
            throw new \Exception('Refund amount cannot exceed payment amount');
        }

        $payment->status = Payment::STATUS_REFUNDED;
        $payment->notes = ($payment->notes ? $payment->notes."\n" : '').'Refund: Rp '.number_format($amount, 0, ',', '.')." - Reason: {$reason}";
        $payment->save();

        return true;
    }
}
