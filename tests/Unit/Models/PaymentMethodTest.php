<?php

namespace Tests\Unit\Models;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_payment_method(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'name' => 'Transfer Bank BCA',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'PT Bintang Agung Tani',
            'logo' => 'payment-methods/bca.png',
            'instructions' => 'Silakan transfer ke rekening BCA',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
            'name' => 'Transfer Bank BCA',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'PT Bintang Agung Tani',
            'logo' => 'payment-methods/bca.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(PaymentMethod::class, $paymentMethod);
    }

    public function test_can_get_logo_url(): void
    {
        Storage::fake('public');

        $paymentMethodWithLogo = PaymentMethod::factory()->create([
            'logo' => 'payment-methods/bca.png',
        ]);

        $paymentMethodWithoutLogo = PaymentMethod::factory()->create([
            'logo' => null,
        ]);

        $this->assertEquals(Storage::url('payment-methods/bca.png'), $paymentMethodWithLogo->getLogoUrl());
        $this->assertNull($paymentMethodWithoutLogo->getLogoUrl());
    }

    public function test_scope_active_works(): void
    {
        // Create active payment methods
        $active1 = PaymentMethod::factory()->create(['is_active' => true, 'name' => 'Active 1']);
        $active2 = PaymentMethod::factory()->create(['is_active' => true, 'name' => 'Active 2']);

        // Create inactive payment method
        $inactive = PaymentMethod::factory()->create(['is_active' => false, 'name' => 'Inactive']);

        $activeMethods = PaymentMethod::active()->get();

        $this->assertCount(2, $activeMethods);
        $this->assertTrue($activeMethods->contains($active1->id));
        $this->assertTrue($activeMethods->contains($active2->id));
        $this->assertFalse($activeMethods->contains($inactive->id));
    }

    public function test_scope_ordered_works(): void
    {
        PaymentMethod::factory()->create(['sort_order' => 3, 'name' => 'Third']);
        PaymentMethod::factory()->create(['sort_order' => 1, 'name' => 'First']);
        PaymentMethod::factory()->create(['sort_order' => 2, 'name' => 'Second']);

        $orderedMethods = PaymentMethod::ordered()->get();

        $this->assertEquals('First', $orderedMethods->first()->name);
        $this->assertEquals('Second', $orderedMethods->skip(1)->first()->name);
        $this->assertEquals('Third', $orderedMethods->skip(2)->first()->name);
    }

    public function test_get_full_bank_info_returns_correct_format(): void
    {
        $paymentMethod = PaymentMethod::factory()->create([
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'PT Bintang Agung Tani',
        ]);

        $expectedFormat = 'BCA - 1234567890 a.n. PT Bintang Agung Tani';

        $this->assertEquals($expectedFormat, $paymentMethod->getFullBankInfo());
    }

    public function test_has_orders_relationship(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $paymentMethod->orders());
    }

    public function test_has_payment_proofs_relationship(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $paymentMethod->paymentProofs());
    }

    public function test_casts_is_active_as_boolean(): void
    {
        $paymentMethod = PaymentMethod::factory()->create(['is_active' => 1]);

        $this->assertIsBool($paymentMethod->is_active);
        $this->assertTrue($paymentMethod->is_active);
    }

    public function test_casts_sort_order_as_integer(): void
    {
        $paymentMethod = PaymentMethod::factory()->create(['sort_order' => '5']);

        $this->assertIsInt($paymentMethod->sort_order);
        $this->assertEquals(5, $paymentMethod->sort_order);
    }

    public function test_factory_can_create_inactive_state(): void
    {
        $inactiveMethod = PaymentMethod::factory()->inactive()->create();

        $this->assertFalse($inactiveMethod->is_active);
    }
}
