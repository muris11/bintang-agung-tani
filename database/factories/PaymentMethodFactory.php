<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        $banks = [
            ['name' => 'Bank BRI', 'bank_name' => 'BRI', 'account_number' => '002123456789'],
            ['name' => 'Bank BCA', 'bank_name' => 'BCA', 'account_number' => '1234567890'],
            ['name' => 'Bank BNI', 'bank_name' => 'BNI', 'account_number' => '0012345678'],
            ['name' => 'Bank Mandiri', 'bank_name' => 'Mandiri', 'account_number' => '1230001234567'],
        ];

        $bank = fake()->randomElement($banks);

        return [
            'name' => $bank['name'],
            'bank_name' => $bank['bank_name'],
            'account_number' => $bank['account_number'],
            'account_name' => 'PT Bintang Agung Tani',
            'logo' => 'payment-methods/'.strtolower($bank['bank_name']).'.png',
            'instructions' => fake()->optional()->paragraph(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
