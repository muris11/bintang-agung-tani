<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'payment_method' => fake()->randomElement(['bank_transfer', 'e-wallet', 'cod']),
            'provider' => 'manual',
            'provider_transaction_id' => fake()->optional()->uuid(),
            'provider_status' => fake()->optional()->word(),
            'amount' => fake()->randomFloat(2, 10000, 1000000),
            'status' => fake()->randomElement([
                Payment::STATUS_PENDING,
                Payment::STATUS_SUCCESS,
                Payment::STATUS_FAILED,
                Payment::STATUS_EXPIRED,
                Payment::STATUS_REFUNDED,
            ]),
            'payment_data' => fake()->optional()->passthrough([]),
            'notes' => fake()->optional()->sentence(),
            'paid_at' => fake()->optional()->dateTime(),
            'expired_at' => fake()->optional()->dateTimeBetween('now', '+1 day'),
        ];
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_PENDING,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is successful.
     */
    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_SUCCESS,
            'paid_at' => fake()->dateTime(),
        ]);
    }

    /**
     * Indicate that the payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_FAILED,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is manual (for verification).
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'manual',
        ]);
    }
}
