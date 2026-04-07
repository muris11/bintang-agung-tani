<?php

namespace App\Services;

use App\Exceptions\PaymentVerificationException;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PaymentProofService
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
    ];

    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    public function upload(
        Order $order,
        User $user,
        PaymentMethod $paymentMethod,
        UploadedFile $file,
        ?string $notes = null
    ): PaymentProof {
        $this->validateFile($file);

        $imagePath = $this->processAndSaveImage($file);

        $proof = PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'image_path' => $imagePath,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'notes' => $notes,
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        // Update order status to waiting for verification
        $previousStatus = $order->status;
        $order->updateStatus(Order::STATUS_MENUNGGU_VERIFIKASI, 'Bukti pembayaran diupload, menunggu verifikasi admin');

        // Dispatch event untuk notifikasi admin
        event(new \App\Events\PaymentProofUploaded($order, $proof, $user->id));

        return $proof;
    }

    private function validateFile(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \InvalidArgumentException('Invalid file type');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File too large');
        }
    }

    private function processAndSaveImage(UploadedFile $file): string
    {
        $image = $this->imageManager->decodePath($file->getRealPath());

        $width = $image->width();
        $height = $image->height();

        if ($width > 1920 || $height > 1080) {
            $image->scaleDown(1920, 1080);
        }

        $filename = 'payment_proofs/'.uniqid().'.jpg';

        $encoded = $image->encode(new \Intervention\Image\Encoders\JpegEncoder(quality: 85));
        Storage::disk('public')->put($filename, $encoded->toString());

        return $filename;
    }

    public function verify(PaymentProof $proof, User $admin, ?string $notes = null): void
    {
        if ($proof->status !== PaymentProof::STATUS_PENDING) {
            throw new PaymentVerificationException(
                $proof->id,
                'Payment proof is not in pending status',
                'Status bukti pembayaran bukan pending'
            );
        }

        $proof->markAsVerified($admin->id, $notes);

        $order = $proof->order;
        $order->markAsPaid($order->total_amount, $proof->paymentMethod->name, $admin->id);

        // Update order status to processing after verification
        $previousStatus = $order->status;
        $order->updateStatus(Order::STATUS_PROCESSING, 'Pembayaran terverifikasi oleh admin', $admin->id);

        // Dispatch events for notifications
        event(new \App\Events\PaymentVerified($order, $admin->id, $notes));
        event(new \App\Events\OrderStatusChanged($order->fresh(), $previousStatus, 'Pembayaran terverifikasi oleh admin', $admin->id));
    }

    public function reject(PaymentProof $proof, User $admin, string $reason): void
    {
        if ($proof->status !== PaymentProof::STATUS_PENDING) {
            throw new PaymentVerificationException(
                $proof->id,
                'Cannot reject non-pending payment proof',
                'Bukti pembayaran tidak dapat ditolak'
            );
        }

        $proof->markAsRejected($admin->id, $reason);
    }

    public function deleteProof(PaymentProof $proof): void
    {
        if (! empty($proof->image_path)) {
            Storage::disk('public')->delete($proof->image_path);
        }

        $proof->delete();
    }
}
