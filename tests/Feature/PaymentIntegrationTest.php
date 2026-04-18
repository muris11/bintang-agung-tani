<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup storage for tests
        Storage::fake('payment_proofs');
        Storage::fake('qr_codes');
    }

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    protected function createProduct(float $price = 10000, int $stock = 50): Product
    {
        $category = Category::factory()->create();

        return Product::factory()->create([
            'category_id' => $category->id,
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    protected function createCartWithItems(User $user, array $productsWithQty): Cart
    {
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 0,
            'total_items' => 0,
        ]);

        $totalAmount = 0;
        $totalItems = 0;

        foreach ($productsWithQty as $data) {
            $product = $data['product'];
            $quantity = $data['quantity'];
            $subtotal = $product->price * $quantity;

            CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
            ]);

            $totalAmount += $subtotal;
            $totalItems += $quantity;
        }

        $cart->total_amount = $totalAmount;
        $cart->total_items = $totalItems;
        $cart->save();

        return $cart;
    }

    protected function createPaymentMethods(): void
    {
        PaymentMethod::create([
            'name' => 'BRI',
            'bank_name' => 'Bank Rakyat Indonesia',
            'account_number' => '123456789012345',
            'account_name' => 'PT Bintang Agung Tani',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PaymentMethod::create([
            'name' => 'BCA',
            'bank_name' => 'Bank Central Asia',
            'account_number' => '9876543210',
            'account_name' => 'PT Bintang Agung Tani',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        PaymentMethod::create([
            'name' => 'Mandiri',
            'bank_name' => 'Bank Mandiri',
            'account_number' => '555566667777',
            'account_name' => 'PT Bintang Agung Tani',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }

    public function test_complete_payment_flow(): void
    {
        // Create users
        $user = $this->createRegularUser();
        $admin = $this->createAdminUser();

        // Create products and payment methods
        $product1 = $this->createProduct(50000, 50);
        $product2 = $this->createProduct(75000, 30);
        $this->createPaymentMethods();

        // Step 1: User creates cart with items
        $cart = $this->createCartWithItems($user, [
            ['product' => $product1, 'quantity' => 2],
            ['product' => $product2, 'quantity' => 1],
        ]);

        $this->assertEquals(2, $cart->items()->count());
        $expectedCartTotal = (50000 * 2) + (75000 * 1); // 175000
        $this->assertEquals($expectedCartTotal, $cart->total_amount);

        // Step 2: User creates order from cart (checkout)
        $shippingCost = 15000;
        $response = $this->actingAs($user)->post(route('user.checkout.store'), [
            'shipping_address' => 'Jl. Test No. 123, Jakarta',
            'shipping_phone' => '081234567890',
            'shipping_cost' => $shippingCost,
            'shipping_courier' => 'jne',
            'notes' => 'Test order integration',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify order was created
        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($expectedCartTotal, $order->subtotal);
        $this->assertEquals($shippingCost, $order->shipping_cost);
        $this->assertEquals($expectedCartTotal + $shippingCost, $order->total_amount);

        // Verify cart was cleared
        $cart->refresh();
        $this->assertEquals(0, $cart->items()->count());

        // Step 3: User selects payment method with QR code generation
        $paymentMethod = PaymentMethod::where('name', 'BCA')->first();
        $this->assertNotNull($paymentMethod);

        // Update order with payment method
        $order->payment_method_id = $paymentMethod->id;
        $order->status = Order::STATUS_PENDING;
        $order->save();

        // Generate QR code data (simulating QR code generation)
        $qrCodeData = $order->generateQrCodeData();
        $this->assertStringContainsString($order->order_number, $qrCodeData);
        $this->assertStringContainsString((string) $order->total_amount, $qrCodeData);

        // Step 4: User uploads payment proof
        // First update order status to menunggu_verifikasi
        $order->status = Order::STATUS_MENUNGGU_VERIFIKASI;
        $order->save();

        $this->assertTrue($order->isMenungguVerifikasi());

        // Create a fake payment proof image
        $paymentProofFile = UploadedFile::fake()->image('payment-proof.jpg');

        // Store the payment proof
        $imagePath = $paymentProofFile->store('payment-proofs', 'payment_proofs');

        $paymentProof = PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'image_path' => $imagePath,
            'original_filename' => $paymentProofFile->getClientOriginalName(),
            'file_size' => $paymentProofFile->getSize(),
            'notes' => 'Bukti pembayaran via BCA',
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        // Verify payment proof was created
        $this->assertDatabaseHas('payment_proofs', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        // Verify order can upload proof
        $this->assertTrue($order->canUploadProof());

        // Verify the file exists in storage
        Storage::disk('payment_proofs')->assertExists($imagePath);

        // Step 5: Admin verifies the payment proof
        $this->actingAs($admin);

        // Verify admin can view the payment proof
        $order->refresh();
        $latestProof = $order->latestPaymentProof;
        $this->assertNotNull($latestProof);
        $this->assertEquals(PaymentProof::STATUS_PENDING, $latestProof->status);

        // Admin verifies the payment
        $latestProof->markAsVerified($admin->id, 'Pembayaran sudah diterima dan diverifikasi');

        // Verify payment proof status changed to verified
        $latestProof->refresh();
        $this->assertEquals(PaymentProof::STATUS_VERIFIED, $latestProof->status);
        $this->assertNotNull($latestProof->verified_at);
        $this->assertEquals($admin->id, $latestProof->verified_by);

        // Step 6: Update order status to paid/processing after verification
        $order->updateStatus(Order::STATUS_PROCESSING, 'Pembayaran diverifikasi, pesanan diproses', $admin->id);
        $order->paid_amount = $order->total_amount;
        $order->paid_at = now();
        $order->save();

        // Verify order status updated
        $order->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);
        $this->assertTrue($order->isPaid());
        $this->assertNotNull($order->paid_at);

        // Verify order timeline
        $timeline = $order->getTimeline();
        $this->assertNotEmpty($timeline);
        $this->assertEquals('created', $timeline[0]['status']);

        // Verify status history was recorded
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => Order::STATUS_PROCESSING,
        ]);
    }

    public function test_qr_code_generation_for_order(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct(100000);

        $cart = $this->createCartWithItems($user, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $response = $this->actingAs($user)->post(route('user.checkout.store'), [
            'shipping_address' => 'Jl. Test No. 456',
            'shipping_phone' => '089876543210',
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);

        // Generate QR code data
        $qrCodeData = $order->generateQrCodeData();
        $decodedData = json_decode($qrCodeData, true);

        // Verify QR code contains order information
        $this->assertEquals($order->id, $decodedData['order_id']);
        $this->assertEquals($order->order_number, $decodedData['order_number']);
        $this->assertEquals($order->total_amount, $decodedData['total']);
        $this->assertArrayHasKey('timestamp', $decodedData);
    }

    public function test_payment_proof_upload_and_verification(): void
    {
        Storage::fake('payment_proofs');

        $user = $this->createRegularUser();
        $admin = $this->createAdminUser();
        $this->createPaymentMethods();

        $product = $this->createProduct(50000);
        $cart = $this->createCartWithItems($user, [
            ['product' => $product, 'quantity' => 2],
        ]);

        // Create order
        $this->actingAs($user)->post(route('user.checkout.store'), [
            'shipping_address' => 'Jl. Test No. 789',
            'shipping_phone' => '081122334455',
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $paymentMethod = PaymentMethod::first();

        $order->payment_method_id = $paymentMethod->id;
        $order->status = Order::STATUS_MENUNGGU_VERIFIKASI;
        $order->save();

        // Upload payment proof
        $paymentProofFile = UploadedFile::fake()->image('bukti-transfer.jpg');
        $imagePath = $paymentProofFile->store('proofs', 'payment_proofs');

        $paymentProof = PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'image_path' => $imagePath,
            'original_filename' => $paymentProofFile->getClientOriginalName(),
            'file_size' => $paymentProofFile->getSize(),
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        // Verify initial state
        $this->assertTrue($paymentProof->isPending());
        $this->assertFalse($paymentProof->isVerified());
        $this->assertFalse($paymentProof->isRejected());

        // Admin verifies
        $paymentProof->markAsVerified($admin->id, 'Transfer valid');
        $paymentProof->refresh();

        // Verify final state
        $this->assertFalse($paymentProof->isPending());
        $this->assertTrue($paymentProof->isVerified());
        $this->assertFalse($paymentProof->isRejected());
        $this->assertEquals('Terverifikasi', $paymentProof->getStatusLabel());
        $this->assertEquals('green', $paymentProof->getStatusColor());
    }

    public function test_order_status_transitions(): void
    {
        $user = $this->createRegularUser();
        $product = $this->createProduct(100000);

        $cart = $this->createCartWithItems($user, [
            ['product' => $product, 'quantity' => 1],
        ]);

        // Initial status: pending (after checkout)
        $response = $this->actingAs($user)->post(route('user.checkout.store'), [
            'shipping_address' => 'Jl. Test Address',
            'shipping_phone' => '081234567890',
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertTrue($order->isPending());
        $this->assertFalse($order->isPaid());

        // Status: pending
        $order->status = Order::STATUS_PENDING;
        $order->save();
        $this->assertTrue($order->isPaymentPending());

        // Status: menunggu_verifikasi (after proof upload)
        $order->status = Order::STATUS_MENUNGGU_VERIFIKASI;
        $order->save();
        $this->assertTrue($order->isMenungguVerifikasi());
        $this->assertTrue($order->canUploadProof());

        // Status: processing (after verification)
        $order->status = Order::STATUS_PROCESSING;
        $order->paid_amount = $order->total_amount;
        $order->save();
        $this->assertTrue($order->isProcessing());
        $this->assertTrue($order->isPaid());
    }

    public function test_payment_rejection_flow(): void
    {
        Storage::fake('payment_proofs');

        $user = $this->createRegularUser();
        $admin = $this->createAdminUser();
        $this->createPaymentMethods();

        $product = $this->createProduct(50000);
        $cart = $this->createCartWithItems($user, [
            ['product' => $product, 'quantity' => 1],
        ]);

        // Create order
        $this->actingAs($user)->post(route('user.checkout.store'), [
            'shipping_address' => 'Jl. Test Address',
            'shipping_phone' => '081234567890',
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $paymentMethod = PaymentMethod::first();

        $order->payment_method_id = $paymentMethod->id;
        $order->status = Order::STATUS_MENUNGGU_VERIFIKASI;
        $order->save();

        // Upload payment proof
        $paymentProofFile = UploadedFile::fake()->image('bukti.jpg');
        $imagePath = $paymentProofFile->store('proofs', 'payment_proofs');

        $paymentProof = PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'image_path' => $imagePath,
            'original_filename' => $paymentProofFile->getClientOriginalName(),
            'file_size' => $paymentProofFile->getSize(),
            'status' => PaymentProof::STATUS_PENDING,
        ]);

        // Admin rejects the payment
        $paymentProof->markAsRejected($admin->id, 'Bukti pembayaran tidak jelas');
        $paymentProof->refresh();

        // Verify rejection
        $this->assertEquals(PaymentProof::STATUS_REJECTED, $paymentProof->status);
        $this->assertTrue($paymentProof->isRejected());
        $this->assertEquals('Ditolak', $paymentProof->getStatusLabel());
        $this->assertEquals('red', $paymentProof->getStatusColor());
        $this->assertNotNull($paymentProof->verified_at);
        $this->assertEquals($admin->id, $paymentProof->verified_by);
        $this->assertEquals('Bukti pembayaran tidak jelas', $paymentProof->admin_notes);

        // Order should still be in menunggu_verifikasi or allow new upload
        $order->refresh();
        $this->assertTrue($order->canUploadProof());
    }

    public function test_multiple_payment_methods_available(): void
    {
        $this->createPaymentMethods();

        $methods = PaymentMethod::all();
        $this->assertCount(3, $methods);

        // Verify BRI
        $bri = PaymentMethod::where('name', 'BRI')->first();
        $this->assertEquals('Bank Rakyat Indonesia', $bri->bank_name);
        $this->assertEquals('123456789012345', $bri->account_number);
        $this->assertEquals('PT Bintang Agung Tani', $bri->account_name);

        // Verify BCA
        $bca = PaymentMethod::where('name', 'BCA')->first();
        $this->assertEquals('Bank Central Asia', $bca->bank_name);
        $this->assertEquals('9876543210', $bca->account_number);
        $this->assertEquals('PT Bintang Agung Tani', $bca->account_name);

        // Verify Mandiri
        $mandiri = PaymentMethod::where('name', 'Mandiri')->first();
        $this->assertEquals('Bank Mandiri', $mandiri->bank_name);
        $this->assertEquals('555566667777', $mandiri->account_number);
        $this->assertEquals('PT Bintang Agung Tani', $mandiri->account_name);

        // Test full bank info format
        $this->assertEquals(
            'Bank Rakyat Indonesia - 123456789012345 a.n. PT Bintang Agung Tani',
            $bri->getFullBankInfo()
        );
    }
}
