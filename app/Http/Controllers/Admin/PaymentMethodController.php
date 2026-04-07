<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function __construct(
        private PaymentMethodService $paymentMethodService
    ) {}

    public function index(): View
    {
        $paymentMethods = PaymentMethod::withCount('orders')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create(): View
    {
        return view('admin.payment-methods.create');
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $logo = $request->file('logo');

            $this->paymentMethodService->create($data, $logo);

            return redirect()
                ->route('admin.payment-methods.index')
                ->with('success', 'Metode pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan metode pembayaran: '.$e->getMessage())
                ->withInput();
        }
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        try {
            $data = $request->validated();
            $logo = $request->file('logo');

            $this->paymentMethodService->update($paymentMethod, $data, $logo);

            return redirect()
                ->route('admin.payment-methods.index')
                ->with('success', 'Metode pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui metode pembayaran: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        try {
            if ($paymentMethod->orders()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Metode pembayaran tidak dapat dihapus karena sudah digunakan dalam pesanan.');
            }

            $this->paymentMethodService->delete($paymentMethod);

            return redirect()
                ->route('admin.payment-methods.index')
                ->with('success', 'Metode pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus metode pembayaran: '.$e->getMessage());
        }
    }

    public function toggleActive(PaymentMethod $paymentMethod): RedirectResponse
    {
        try {
            $paymentMethod->update([
                'is_active' => ! $paymentMethod->is_active,
            ]);

            $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()
                ->back()
                ->with('success', "Metode pembayaran berhasil {$status}.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status metode pembayaran: '.$e->getMessage());
        }
    }
}
