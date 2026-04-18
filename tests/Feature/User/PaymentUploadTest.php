<?php

namespace Tests\Feature\User;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $order;

    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create([
            'is_active' => true,
        ]);
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'payment_method_id' => null,
        ]);
    }

    /**
     * @test
     */
    public function test_user_can_select_payment_method()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.payments.select-method', $this->order));

        $response->assertOk()
            ->assertViewIs('user.payments.select-method')
            ->assertViewHas('order')
            ->assertViewHas('paymentMethods');
    }

    /**
     * @test
     */
    public function test_user_can_store_payment_method()
    {
        $response = $this->actingAs($this->user)
            ->post(route('user.payments.store-method', $this->order), [
                'payment_method_id' => $this->paymentMethod->id,
            ]);

        $response->assertRedirect(route('user.payments.show-upload', $this->order));
        $this->assertEquals($this->paymentMethod->id, $this->order->fresh()->payment_method_id);
    }

    /**
     * @test
     */
    public function test_user_can_view_upload_form()
    {
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        $response = $this->actingAs($this->user)
            ->get(route('user.payments.show-upload', $this->order));

        $response->assertOk()
            ->assertViewIs('user.payments.upload-form')
            ->assertViewHas('order');
    }

    /**
     * @test
     */
    public function test_user_can_upload_payment_proof()
    {
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        $file = UploadedFile::fake()->image('payment-proof.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('user.payments.upload-proof', $this->order), [
                'proof_image' => $file,
                'notes' => 'Pembayaran melalui transfer bank',
            ]);

        $response->assertRedirect();
        $this->order->refresh();
    }

    /**
     * @test
     */
    public function test_unauthorized_user_cannot_access_other_users_order()
    {
        $otherUser = User::factory()->create();
        $otherOrder = Order::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.payments.select-method', $otherOrder));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_validation_errors_for_missing_proof_image()
    {
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        $response = $this->actingAs($this->user)
            ->post(route('user.payments.upload-proof', $this->order), [
                'notes' => 'Catatan pembayaran',
            ]);

        $response->assertSessionHasErrors(['proof_image']);
    }

    /**
     * @test
     */
    public function test_validation_errors_for_invalid_file_type()
    {
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)
            ->post(route('user.payments.upload-proof', $this->order), [
                'proof_image' => $invalidFile,
            ]);

        $response->assertSessionHasErrors(['proof_image']);
    }

    /**
     * @test
     */
    public function test_validation_errors_for_file_too_large()
    {
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        $largeFile = UploadedFile::fake()->image('large.jpg')->size(6000);

        $response = $this->actingAs($this->user)
            ->post(route('user.payments.upload-proof', $this->order), [
                'proof_image' => $largeFile,
            ]);

        $response->assertSessionHasErrors(['proof_image']);
    }

    /**
     * @test
     */
    public function test_admin_cannot_upload_proof()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        $file = UploadedFile::fake()->image('payment-proof.jpg');

        $response = $this->actingAs($admin)
            ->post(route('user.payments.upload-proof', $this->order), [
                'proof_image' => $file,
            ]);

        // Admin is redirected to admin dashboard by UserMiddleware
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * @test
     */
    public function test_user_can_view_qr_code()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.payments.qr-code', $this->order));

        $response->assertOk()
            ->assertViewIs('user.payments.qr-code');
    }

    /**
     * @test
     */
    public function test_user_can_download_qr_code()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.payments.download-qr', $this->order));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    /**
     * @test
     */
    public function test_upload_blocked_when_cannot_upload_proof()
    {
        // Order that cannot upload proof (e.g., already completed)
        $completedOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_COMPLETED,
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.payments.select-method', $completedOrder));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_user_is_redirected_to_order_detail_after_upload()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        $file = UploadedFile::fake()->image('payment_proof.jpg');

        $response = $this->actingAs($user)->post(
            route('user.payments.upload-proof', $order),
            ['proof_image' => $file, 'notes' => 'Test payment']
        );

        $response->assertRedirect(route('user.orders.show', $order));
        $response->assertSessionHas('success', 'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi dalam 1x24 jam.');
    }

    /**
     * @test
     * Comprehensive test for payment proof upload with file verification
     */
    public function test_payment_proof_uploads_correctly_to_storage()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
            'payment_method_id' => $this->paymentMethod->id,
            'total_amount' => 100000,
        ]);

        // Create a fake image file
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        // Submit the upload form
        $response = $this->actingAs($user)->post(
            route('user.payments.upload-proof', $order),
            [
                'proof_image' => $file,
                'notes' => 'Transfer dari Bank BCA',
            ]
        );

        // Assert redirect to order detail
        $response->assertRedirect(route('user.orders.show', $order));
        $response->assertSessionHas('success');

        // Assert database has payment proof record
        $this->assertDatabaseHas('payment_proofs', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $this->paymentMethod->id,
            'status' => 'pending',
            'notes' => 'Transfer dari Bank BCA',
        ]);

        // Assert order status changed to menunggu_verifikasi
        $order->refresh();
        $this->assertEquals(Order::STATUS_MENUNGGU_VERIFIKASI, $order->status);

        // Verify the uploaded file exists in storage
        $proof = \App\Models\PaymentProof::where('order_id', $order->id)->first();
        $this->assertNotNull($proof);
        $this->assertNotNull($proof->image_path);
        Storage::disk('public')->assertExists($proof->image_path);
    }

    /**
     * @test
     * Test validation rejects invalid file types and sizes
     */
    public function test_payment_upload_validation_rejects_invalid_files()
    {
        $this->order->update(['payment_method_id' => $this->paymentMethod->id]);

        // Test with non-image file (PDF)
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)->post(
            route('user.payments.upload-proof', $this->order),
            ['proof_image' => $pdfFile]
        );

        $response->assertSessionHasErrors('proof_image');

        // Test with file too large (6MB)
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(6000);

        $response = $this->actingAs($this->user)->post(
            route('user.payments.upload-proof', $this->order),
            ['proof_image' => $largeFile]
        );

        $response->assertSessionHasErrors('proof_image');

        // Test with text file disguised as image
        $textFile = UploadedFile::fake()->create('fake.jpg', 100, 'text/plain');

        $response = $this->actingAs($this->user)->post(
            route('user.payments.upload-proof', $this->order),
            ['proof_image' => $textFile]
        );

        $response->assertSessionHasErrors('proof_image');
    }
}
