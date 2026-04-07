<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Services\PaymentProofService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentVerificationController extends Controller
{
    public function __construct(
        private PaymentProofService $paymentProofService
    ) {}

    public function index(Request $request): View
    {
        $query = PaymentProof::with(['order', 'order.items', 'order.items.product', 'user', 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        $status = $request->get('status');
        if ($status && in_array($status, [PaymentProof::STATUS_PENDING, PaymentProof::STATUS_VERIFIED, PaymentProof::STATUS_REJECTED])) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $paymentProofs = $query->paginate(20)->withQueryString();

        return view('admin.payment-proofs.index', compact('paymentProofs', 'status'));
    }

    public function show(PaymentProof $paymentProof): View
    {
        $paymentProof->load([
            'order',
            'order.items',
            'order.items.product',
            'user',
            'paymentMethod',
            'verifier',
        ]);

        return view('admin.payment-proofs.show', compact('paymentProof'));
    }

    public function verify(Request $request, PaymentProof $paymentProof): RedirectResponse
    {
        if (! $paymentProof->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'Bukti pembayaran sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->paymentProofService->verify($paymentProof, auth()->user(), $validated['notes'] ?? null);

            return redirect()
                ->route('admin.payment-proofs.index')
                ->with('success', 'Bukti pembayaran berhasil diverifikasi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memverifikasi bukti pembayaran: '.$e->getMessage());
        }
    }

    public function reject(Request $request, PaymentProof $paymentProof): RedirectResponse
    {
        if (! $paymentProof->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'Bukti pembayaran sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->paymentProofService->reject($paymentProof, auth()->user(), $validated['reason']);

            return redirect()
                ->route('admin.payment-proofs.index')
                ->with('success', 'Bukti pembayaran berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menolak bukti pembayaran: '.$e->getMessage());
        }
    }
}
