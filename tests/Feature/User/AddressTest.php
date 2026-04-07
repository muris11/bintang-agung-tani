<?php

namespace Tests\Feature\User;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_admin' => false,
        ]);
    }

    public function test_user_can_view_addresses(): void
    {
        $this->actingAs($this->user);

        // Create addresses for user
        Address::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('user.alamat.index'));

        $response->assertStatus(200);
        $response->assertViewIs('user.alamat');
    }

    public function test_user_can_create_address(): void
    {
        $this->actingAs($this->user);

        $addressData = [
            'label' => 'Rumah',
            'recipient_name' => 'John Doe',
            'phone' => '081234567890',
            'full_address' => 'Jl. Mawar No. 123',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'postal_code' => '12345',
            'is_default' => true,
            'notes' => 'Dekat minimarket',
        ];

        $response = $this->post(route('user.alamat.store'), $addressData);

        $response->assertRedirect(route('user.alamat.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'label' => 'Rumah',
            'recipient_name' => 'John Doe',
            'is_default' => true,
        ]);
    }

    public function test_user_can_update_address(): void
    {
        $this->actingAs($this->user);

        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'label' => 'Rumah Lama',
        ]);

        $updateData = [
            'label' => 'Rumah Baru',
            'recipient_name' => $address->recipient_name,
            'phone' => $address->phone,
            'full_address' => $address->full_address,
            'province' => $address->province,
            'city' => $address->city,
            'district' => $address->district,
            'postal_code' => $address->postal_code,
        ];

        $response = $this->put(route('user.alamat.update', $address), $updateData);

        $response->assertRedirect(route('user.alamat.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'Rumah Baru',
        ]);
    }

    public function test_user_can_delete_address(): void
    {
        $this->actingAs($this->user);

        $address = Address::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->delete(route('user.alamat.destroy', $address));

        $response->assertRedirect(route('user.alamat.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_user_can_set_default_address(): void
    {
        $this->actingAs($this->user);

        // Create two addresses, first one is default
        $address1 = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
        ]);

        $address2 = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
        ]);

        // Set second address as default
        $response = $this->patch(route('user.alamat.default', $address2));

        $response->assertRedirect(route('user.alamat.index'));
        $response->assertSessionHas('success');

        // First address should no longer be default
        $this->assertDatabaseHas('addresses', [
            'id' => $address1->id,
            'is_default' => false,
        ]);

        // Second address should now be default
        $this->assertDatabaseHas('addresses', [
            'id' => $address2->id,
            'is_default' => true,
        ]);
    }

    public function test_first_address_becomes_default_automatically(): void
    {
        $this->actingAs($this->user);

        $addressData = [
            'label' => 'Rumah',
            'recipient_name' => 'John Doe',
            'phone' => '081234567890',
            'full_address' => 'Jl. Mawar No. 123',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'postal_code' => '12345',
            'is_default' => false,
        ];

        $this->post(route('user.alamat.store'), $addressData);

        // Should become default automatically since it's the first address
        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'is_default' => true,
        ]);
    }

    public function test_user_cannot_access_other_user_addresses(): void
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherAddress = Address::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        // Try to update other user's address
        $response = $this->put(route('user.alamat.update', $otherAddress), [
            'label' => 'Hacked',
            'recipient_name' => 'Hacker',
            'phone' => '081234567890',
            'full_address' => 'Jl. Hacked',
            'province' => 'Hacked',
            'city' => 'Hacked',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
