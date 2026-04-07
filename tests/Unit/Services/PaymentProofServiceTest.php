<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use App\Services\PaymentProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentProofService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentProofService;
        Storage::fake('public');
    }

    public function test_can_upload_payment_proof(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100000,
        ]);
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $file = UploadedFile::fake()->image('payment-proof.jpg', 800, 600);

        $proof = $this->service->upload(
            $order,
            $user,
            $paymentMethod,
            $file,
            'Payment from mobile banking'
        );

        $this->assertInstanceOf(PaymentProof::class, $proof);
        $this->assertEquals($order->id, $proof->order_id);
        $this->assertEquals($user->id, $proof->user_id);
        $this->assertEquals($paymentMethod->id, $proof->payment_method_id);
        $this->assertEquals('pending', $proof->status);
        $this->assertNull($proof->verified_at);
        $this->assertEquals('Payment from mobile banking', $proof->notes);
        $this->assertNotNull($proof->image_path);
        $this->assertNotNull($proof->original_filename);
        $this->assertNotNull($proof->file_size);

        Storage::disk('public')->assertExists($proof->image_path);

        $order->refresh();
        $this->assertEquals(Order::STATUS_MENUNGGU_VERIFIKASI, $order->status);
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100000,
        ]);
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $file = UploadedFile::fake()->create('payment-proof.pdf', 100, 'application/pdf');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file type');

        $this->service->upload($order, $user, $paymentMethod, $file);
    }

    public function test_upload_rejects_oversized_file(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100000,
        ]);
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $file = UploadedFile::fake()->image('payment-proof.jpg')->size(6000);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File too large');

        $this->service->upload($order, $user, $paymentMethod, $file);
    }

    public function test_verify_payment_proof(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
            'total_amount' => 100000,
        ]);
        $proof = PaymentProof::factory()->create([
            'order_id' => $order->id,
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        $this->service->verify($proof, $admin, 'Payment verified successfully');

        $proof->refresh();
        $this->assertEquals(PaymentProof::STATUS_VERIFIED, $proof->status);
        $this->assertEquals($admin->id, $proof->verified_by);
        $this->assertNotNull($proof->verified_at);
        $this->assertEquals('Payment verified successfully', $proof->admin_notes);

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);
    }

    public function test_reject_payment_proof(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
            'total_amount' => 100000,
        ]);
        $proof = PaymentProof::factory()->create([
            'order_id' => $order->id,
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        $this->service->reject($proof, $admin, 'Insufficient payment amount');

        $proof->refresh();
        $this->assertEquals(PaymentProof::STATUS_REJECTED, $proof->status);
        $this->assertEquals($admin->id, $proof->verified_by);
        $this->assertNotNull($proof->verified_at);
        $this->assertEquals('Insufficient payment amount', $proof->admin_notes);
    }

    public function test_delete_payment_proof(): void
    {
        $order = Order::factory()->create();
        $proof = PaymentProof::factory()->create([
            'order_id' => $order->id,
            'image_path' => 'payment_proofs/test-image.jpg',
        ]);
        Storage::disk('public')->put('payment_proofs/test-image.jpg', 'dummy content');

        $this->service->deleteProof($proof);

        $this->assertDatabaseMissing('payment_proofs', [
            'id' => $proof->id,
        ]);

        Storage::disk('public')->assertMissing('payment_proofs/test-image.jpg');
    }
}
