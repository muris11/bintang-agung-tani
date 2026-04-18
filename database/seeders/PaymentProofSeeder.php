<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentProofSeeder extends Seeder
{
    /**
     * Seed payment proofs for completed payments.
     */
    public function run(): void
    {
        $payments = Payment::where('status', Payment::STATUS_SUCCESS)->get();

        if ($payments->isEmpty()) {
            $this->command->warn('No successful payments found. Skipping payment proofs.');

            return;
        }

        // Get first payment method
        $paymentMethod = PaymentMethod::first();

        foreach ($payments->take(10) as $payment) { // Create proofs for first 10 payments
            $order = $payment->order;

            if (! $order || ! $paymentMethod) {
                continue;
            }

            PaymentProof::create([
                'order_id' => $order->id,
                'user_id' => $payment->user_id,
                'payment_method_id' => $paymentMethod->id,
                'image_path' => 'payment-proofs/sample-'.$order->order_number.'.jpg',
                'original_filename' => 'bukti-pembayaran-'.$order->order_number.'.jpg',
                'file_size' => fake()->numberBetween(100000, 2000000), // 100KB - 2MB
                'notes' => fake()->optional(0.5)->randomElement([
                    'Pembayaran sesuai tagihan',
                    'Sudah transfer, mohon diverifikasi',
                    'Bukti pembayaran terlampir',
                ]),
                'status' => PaymentProof::STATUS_VERIFIED,
                'admin_notes' => fake()->optional()->randomElement(['OK', 'Sudah dicek', 'Valid']),
                'verified_by' => User::where('is_admin', true)->first()?->id,
                'verified_at' => fake()->dateTimeBetween('-29 days', 'now'),
            ]);
        }

        // Create some pending proofs (not yet verified)
        $pendingPayments = Payment::where('status', Payment::STATUS_PENDING)->take(3)->get();
        foreach ($pendingPayments as $payment) {
            $order = $payment->order;

            if (! $order || ! $paymentMethod) {
                continue;
            }

            PaymentProof::create([
                'order_id' => $order->id,
                'user_id' => $payment->user_id,
                'payment_method_id' => $paymentMethod->id,
                'image_path' => 'payment-proofs/sample-'.$order->order_number.'.jpg',
                'original_filename' => 'bukti-pembayaran-'.$order->order_number.'.jpg',
                'file_size' => fake()->numberBetween(100000, 2000000),
                'notes' => fake()->optional()->sentence(),
                'status' => PaymentProof::STATUS_PENDING,
                'admin_notes' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);
        }

        $this->command->info('Created '.PaymentProof::count().' payment proofs');
    }
}
