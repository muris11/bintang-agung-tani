<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentProof>
 */
class PaymentProofFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentProof::class;

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
            'payment_method_id' => PaymentMethod::factory(),
            'image_path' => 'payment-proofs/'.fake()->uuid().'.jpg',
            'original_filename' => fake()->word().'.jpg',
            'file_size' => fake()->numberBetween(1000, 5000000),
            'notes' => fake()->optional()->sentence(),
            'status' => PaymentProof::STATUS_PENDING,
            'admin_notes' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];
    }

    /**
     * Indicate that the payment proof is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentProof::STATUS_PENDING,
            'verified_by' => null,
            'verified_at' => null,
            'admin_notes' => null,
        ]);
    }

    /**
     * Indicate that the payment proof is verified.
     */
    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            $verifier = User::factory()->admin()->create();

            return [
                'status' => PaymentProof::STATUS_VERIFIED,
                'verified_by' => $verifier->id,
                'verified_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'admin_notes' => fake()->optional()->sentence(),
            ];
        });
    }

    /**
     * Indicate that the payment proof is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            $verifier = User::factory()->admin()->create();

            return [
                'status' => PaymentProof::STATUS_REJECTED,
                'verified_by' => $verifier->id,
                'verified_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'admin_notes' => fake()->sentence(),
            ];
        });
    }
}
