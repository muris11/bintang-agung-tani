<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function checkStatus(Order $order)
    {
        $payment = $this->paymentService->getPaymentByOrder($order);

        if (! $payment) {
            return response()->json([
                'error' => 'Payment not found',
            ], 404);
        }

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'payment' => $payment,
            'status' => $payment->status,
            'is_success' => $payment->isSuccess(),
            'is_pending' => $payment->isPending(),
        ]);
    }

    public function manualConfirm(Request $request, Order $order)
    {
        if (! Auth::user()->is_admin) {
            abort(403, 'Admin access required');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $payment = $this->paymentService->processManualPayment(
                $order,
                $validated,
                Auth::id()
            );

            return redirect()->back()->with('success', 'Payment confirmed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to confirm payment: '.$e->getMessage());
        }
    }

    /**
     * Handle Midtrans webhook with signature verification
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('X-Midtrans-Signature');
        $serverKey = config('midtrans.server_key');
        $rawBody = $request->getContent();

        if (empty($serverKey)) {
            Log::error('Midtrans server key not configured');
            abort(500, 'Payment gateway not configured');
        }

        // Calculate expected signature
        $expectedSignature = hash('sha512', $rawBody.$serverKey);

        // Verify signature
        if (! hash_equals($expectedSignature, $signature)) {
            Log::warning('Invalid Midtrans webhook signature', [
                'ip' => $request->ip(),
                'signature' => $signature,
            ]);
            abort(403, 'Invalid signature');
        }

        $payload = $request->all();

        // Log webhook for debugging
        Log::info('Midtrans webhook received', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
        ]);

        // Find order
        $orderNumber = $payload['order_id'] ?? null;
        if (! $orderNumber) {
            Log::error('Webhook missing order_id');
            abort(400, 'Missing order_id');
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (! $order) {
            Log::error('Order not found in webhook', ['order_number' => $orderNumber]);
            abort(404, 'Order not found');
        }

        // Process based on transaction status
        $status = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        try {
            if ($status === 'capture' || $status === 'settlement') {
                if ($fraudStatus === 'challenge') {
                    // Payment needs review
                    $order->updateStatus(Order::STATUS_MENUNGGU_VERIFIKASI, 'Payment flagged for review');
                } else {
                    // Payment success
                    $order->markAsPaid($order->getRemainingAmount(), 'midtrans');
                    Log::info('Payment confirmed via webhook', ['order_id' => $order->id]);
                }
            } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
                // Payment failed
                Log::info('Payment failed via webhook', [
                    'order_id' => $order->id,
                    'status' => $status,
                ]);
            }

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            abort(500, 'Error processing webhook');
        }
    }
}
