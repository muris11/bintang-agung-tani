<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $query = Payment::with(['order', 'user'])
            ->where('provider', 'manual')
            ->orderBy('created_at', 'desc');

        if (in_array($status, ['pending', 'verified', 'rejected'])) {
            if ($status === 'pending') {
                $query->where('status', Payment::STATUS_PENDING);
            } elseif ($status === 'verified') {
                $query->where('status', Payment::STATUS_SUCCESS);
            } elseif ($status === 'rejected') {
                $query->where('status', Payment::STATUS_FAILED);
            }
        }

        $payments = $query->paginate(20);

        return view('admin.verifikasi', compact('payments', 'status'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['order', 'order.items', 'order.items.product', 'user']);

        return view('admin.detail-verifikasi', compact('payment'));
    }

    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        if (! $payment->isPending()) {
            return redirect()->back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
        }

        try {
            // Update the existing payment to success
            $payment->status = Payment::STATUS_SUCCESS;
            $payment->paid_at = now();
            $payment->notes = ($payment->notes ? $payment->notes."\n" : '').($validated['notes'] ?? 'Pembayaran diverifikasi oleh admin');
            $payment->save();

            // Process the order payment
            $this->paymentService->processManualPayment(
                $payment->order,
                [
                    'payment_method' => $payment->payment_method,
                    'notes' => $validated['notes'] ?? 'Pembayaran diverifikasi oleh admin',
                    'existing_payment_id' => $payment->id,
                ],
                auth()->id()
            );

            ActivityLog::log(
                'payment_verified',
                $payment->order::class,
                $payment->order->id,
                "Pembayaran {$payment->getFormattedAmount()} untuk pesanan {$payment->order->order_number} diverifikasi oleh admin",
                [
                    'payment_id' => $payment->id,
                    'payment_amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'verified_by' => auth()->id(),
                ]
            );

            return redirect()->route('admin.verifikasi.index')->with('success', 'Pembayaran berhasil diverifikasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memverifikasi pembayaran: '.$e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (! $payment->isPending()) {
            return redirect()->back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
        }

        $payment->status = Payment::STATUS_FAILED;
        $payment->notes = ($payment->notes ? $payment->notes."\n" : '').'Ditolak: '.$validated['reason'];
        $payment->save();

        ActivityLog::log(
            'payment_rejected',
            $payment->order::class,
            $payment->order->id,
            "Pembayaran {$payment->getFormattedAmount()} untuk pesanan {$payment->order->order_number} ditolak: {$validated['reason']}",
            [
                'payment_id' => $payment->id,
                'rejected_by' => auth()->id(),
                'reason' => $validated['reason'],
            ]
        );

        return redirect()->route('admin.verifikasi.index')->with('success', 'Pembayaran berhasil ditolak.');
    }
}
