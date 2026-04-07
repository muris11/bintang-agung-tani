<?php

namespace App\Services;

use App\Models\PaymentMethod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentMethodService
{
    /**
     * Get all active payment methods.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, PaymentMethod>
     */
    public function getActiveMethods()
    {
        return PaymentMethod::active()->ordered()->get();
    }

    /**
     * Create a new payment method with optional logo.
     */
    public function create(array $data, ?UploadedFile $logo = null): PaymentMethod
    {
        if ($logo !== null) {
            $data['logo'] = $this->uploadLogo($logo, $data['bank_name'] ?? 'payment');
        }

        return PaymentMethod::create($data);
    }

    /**
     * Update an existing payment method with optional new logo.
     */
    public function update(PaymentMethod $paymentMethod, array $data, ?UploadedFile $logo = null): PaymentMethod
    {
        if ($logo !== null) {
            // Delete old logo if exists
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }

            $data['logo'] = $this->uploadLogo($logo, $data['bank_name'] ?? $paymentMethod->bank_name ?? 'payment');
        }

        $paymentMethod->update($data);

        return $paymentMethod->fresh();
    }

    /**
     * Delete a payment method and its logo.
     */
    public function delete(PaymentMethod $paymentMethod): void
    {
        // Delete logo file if exists
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        $paymentMethod->delete();
    }

    /**
     * Upload a logo file and return the relative path.
     */
    public function uploadLogo(UploadedFile $file, string $bankName = 'payment'): string
    {
        $extension = $file->getClientOriginalExtension();
        $sanitizedBankName = $this->sanitizeFilename($bankName);
        $filename = "{$sanitizedBankName}_".uniqid().".{$extension}";

        return $file->storeAs('payment_methods', $filename, 'public');
    }

    /**
     * Reorder payment methods by their IDs array.
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $index => $id) {
            PaymentMethod::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Sanitize a string to be used in a filename.
     */
    private function sanitizeFilename(string $name): string
    {
        // Remove non-alphanumeric characters and replace spaces with underscores
        $sanitized = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);
        $sanitized = preg_replace('/\s+/', '_', trim($sanitized));

        return strtolower($sanitized) ?: 'payment';
    }
}
