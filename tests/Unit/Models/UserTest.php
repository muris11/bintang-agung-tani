<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_default_address_attribute_that_returns_default_address()
    {
        // Arrange
        $user = User::factory()->create();

        $defaultAddress = Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => $user->name,
            'phone' => '081234567890',
            'full_address' => 'Jl. Test No. 1',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => ' Cidadang',
            'postal_code' => '40111',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $user->id,
            'label' => 'Kantor',
            'recipient_name' => $user->name,
            'phone' => '081234567891',
            'full_address' => 'Jl. Kantor No. 1',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Summarecon',
            'postal_code' => '40111',
            'is_default' => false,
        ]);

        // Act
        $result = $user->defaultAddress;

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($defaultAddress->id, $result->id);
        $this->assertEquals('Rumah', $result->label);
    }

    /** @test */
    public function user_default_address_returns_null_when_no_default_address()
    {
        // Arrange
        $user = User::factory()->create();

        Address::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => $user->name,
            'phone' => '081234567890',
            'full_address' => 'Jl. Test No. 1',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => ' Cidadang',
            'postal_code' => '40111',
            'is_default' => false,
        ]);

        // Act
        $result = $user->defaultAddress;

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function user_default_address_returns_null_when_no_addresses()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $user->defaultAddress;

        // Assert
        $this->assertNull($result);
    }
}
