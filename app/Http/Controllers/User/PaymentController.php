<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Order;
use App\Services\PaymentMethodService;
use App\Services\PaymentProofService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentMethodService $paymentMethodService,
        private PaymentProofService $paymentProofService,
        private QRCodeService $qrCodeService
    ) {}

    /**
     * Show payment method selection page.
     */
    public function selectMethod(Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if order can upload proof
        if (! $order->canUploadProof()) {
            abort(403, 'Order cannot accept payment proof at this time');
        }

        $paymentMethods = $this->paymentMethodService->getActiveMethods();

        return view('user.payments.select-method', compact('order', 'paymentMethods'));
    }

    /**
     * Store selected payment method.
     */
    public function storeMethod(Request $request, Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if order can upload proof
        if (! $order->canUploadProof()) {
            abort(403, 'Order cannot accept payment proof at this time');
        }

        $validated = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ]);

        $order->update([
            'payment_method_id' => $validated['payment_method_id'],
        ]);

        // Generate QR code if needed
        if (empty($order->qr_code_path)) {
            $this->qrCodeService->generateForOrder($order);
        }

        return redirect()->route('user.payments.show-upload', $order)
            ->with('success', 'Metode pembayaran berhasil dipilih.');
    }

    /**
     * Show upload form for payment proof.
     */
    public function showUploadForm(Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if payment method is selected
        if (empty($order->payment_method_id)) {
            return redirect()->route('user.payments.select-method', $order)
                ->with('error', 'Silakan pilih metode pembayaran terlebih dahulu.');
        }

        // Check if order can upload proof
        if (! $order->canUploadProof()) {
            abort(403, 'Order cannot accept payment proof at this time');
        }

        return view('user.payments.upload-form', compact('order'));
    }

    /**
     * Upload payment proof.
     */
    public function uploadProof(UploadPaymentProofRequest $request, Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if order can upload proof
        if (! $order->canUploadProof()) {
            abort(403, 'Order cannot accept payment proof at this time');
        }

        // Ensure payment method is selected
        if (empty($order->payment_method_id)) {
            return redirect()->route('user.payments.select-method', $order)
                ->with('error', 'Silakan pilih metode pembayaran terlebih dahulu.');
        }

        $file = $request->file('proof_image');
        $notes = $request->input('notes');

        $this->paymentProofService->upload(
            $order,
            auth()->user(),
            $order->paymentMethod,
            $file,
            $notes
        );

        return redirect()->route('user.orders.show', $order)
            ->with('success', 'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi dalam 1x24 jam.');
    }

    /**
     * Show QR code for order.
     */
    public function showQRCode(Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Generate QR code if needed
        if (empty($order->qr_code_path)) {
            $this->qrCodeService->generateForOrder($order);
            $order->refresh();
        }

        return view('user.payments.qr-code', compact('order'));
    }

    /**
     * Download QR code PNG file.
     */
    public function downloadQR(Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Generate QR code if needed
        if (empty($order->qr_code_path) || ! Storage::disk('public')->exists($order->qr_code_path)) {
            $this->qrCodeService->generateForOrder($order);
            $order->refresh();
        }

        $filePath = $order->qr_code_path;

        if (! Storage::disk('public')->exists($filePath)) {
            abort(404, 'QR code not found');
        }

        return Storage::disk('public')->download($filePath, "qr-code-{$order->order_number}.png");
    }
}
