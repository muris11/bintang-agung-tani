<?php

namespace Tests\Unit\Services;

use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentMethodServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentMethodService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentMethodService;
        Storage::fake('public');
    }

    public function test_service_class_exists_and_has_expected_methods(): void
    {
        $this->assertInstanceOf(PaymentMethodService::class, $this->service);
        $this->assertTrue(method_exists($this->service, 'create'));
        $this->assertTrue(method_exists($this->service, 'update'));
        $this->assertTrue(method_exists($this->service, 'delete'));
        $this->assertTrue(method_exists($this->service, 'uploadLogo'));
        $this->assertTrue(method_exists($this->service, 'reorder'));
    }

    public function test_create_payment_method_without_logo(): void
    {
        $data = [
            'name' => 'Bank Transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Test Account',
            'is_active' => true,
        ];

        $paymentMethod = $this->service->create($data);

        $this->assertInstanceOf(PaymentMethod::class, $paymentMethod);
        $this->assertEquals('Bank Transfer', $paymentMethod->name);
        $this->assertEquals('BCA', $paymentMethod->bank_name);
        $this->assertNull($paymentMethod->logo);
    }

    public function test_create_payment_method_with_logo(): void
    {
        $logo = UploadedFile::fake()->image('bca_logo.png');

        $data = [
            'name' => 'Bank Transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Test Account',
            'is_active' => true,
        ];

        $paymentMethod = $this->service->create($data, $logo);

        $this->assertInstanceOf(PaymentMethod::class, $paymentMethod);
        $this->assertNotNull($paymentMethod->logo);
        $this->assertStringContainsString('payment_methods/', $paymentMethod->logo);
        Storage::disk('public')->assertExists($paymentMethod->logo);
    }

    public function test_upload_logo_generates_unique_filename(): void
    {
        $logo = UploadedFile::fake()->image('bank_logo.png');

        $path = $this->service->uploadLogo($logo, 'Bank Central Asia');

        $this->assertStringContainsString('payment_methods/', $path);
        $this->assertStringContainsString('bank_central_asia_', $path);
        $this->assertStringEndsWith('.png', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_reorder_updates_sort_order(): void
    {
        $pm1 = PaymentMethod::factory()->create(['sort_order' => 1]);
        $pm2 = PaymentMethod::factory()->create(['sort_order' => 2]);
        $pm3 = PaymentMethod::factory()->create(['sort_order' => 3]);

        $this->service->reorder([$pm3->id, $pm1->id, $pm2->id]);

        $this->assertEquals(2, $pm1->fresh()->sort_order);
        $this->assertEquals(3, $pm2->fresh()->sort_order);
        $this->assertEquals(1, $pm3->fresh()->sort_order);
    }
}
