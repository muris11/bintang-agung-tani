<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(10000, 500000);
        $discountAmount = fake()->boolean(30) ? fake()->numberBetween(1000, $subtotal * 0.2) : 0;
        $shippingCost = fake()->numberBetween(0, 50000);
        $totalAmount = $subtotal - $discountAmount + $shippingCost;

        $status = fake()->randomElement([
            Order::STATUS_PENDING,
            Order::STATUS_MENUNGGU_VERIFIKASI,
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ]);

        $useAddress = fake()->boolean(70);
        $address = $useAddress ? Address::factory()->create() : null;

        $paidAmount = match ($status) {
            Order::STATUS_PROCESSING, Order::STATUS_COMPLETED => $totalAmount,
            Order::STATUS_MENUNGGU_VERIFIKASI => fake()->boolean(80) ? $totalAmount : fake()->numberBetween(0, $totalAmount),
            Order::STATUS_PENDING => fake()->boolean(30) ? fake()->numberBetween(0, $totalAmount) : 0,
            Order::STATUS_CANCELLED => fake()->boolean(50) ? fake()->numberBetween(0, $totalAmount) : 0,
            default => 0,
        };

        $couriers = ['JNE', 'TIKI', 'POS Indonesia', 'J&T Express', 'SiCepat', 'Lion Parcel', 'Ninja Xpress'];

        return [
            'user_id' => User::factory(),
            'address_id' => $address?->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => $status,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_cost' => $shippingCost,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'shipping_courier' => fake()->optional(0.6)->randomElement($couriers),
            'shipping_service' => fake()->optional(0.6)->randomElement(['REG', 'OKE', 'YES', 'JTR', 'EZ']),
            'tracking_number' => fake()->optional(0.5)->regexify('[A-Z]{2}[0-9]{9,12}'),
            'shipping_address_snapshot' => $address?->getCompleteAddressAttribute() ?? fake()->address(),
            'shipping_phone' => $address?->phone ?? fake()->phoneNumber(),
            'payment_method' => fake()->optional(0.8)->randomElement(['Transfer Bank', 'COD', 'QRIS', 'OVO', 'GoPay', 'DANA']),
            'paid_at' => $paidAmount > 0 ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'notes' => fake()->optional(0.3)->sentence(),
            'admin_notes' => fake()->optional(0.1)->sentence(),
        ];
    }

    /**
     * Indicate that the order is in pending status.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Order::STATUS_PENDING,
                'paid_amount' => 0,
                'paid_at' => null,
                'tracking_number' => null,
            ];
        });
    }

    /**
     * Indicate that the order is in processing status.
     */
    public function processing(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Order::STATUS_PROCESSING,
                'paid_amount' => $attributes['total_amount'],
                'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'tracking_number' => null,
            ];
        });
    }

    /**
     * Indicate that the order is waiting for verification.
     */
    public function menungguVerifikasi(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Order::STATUS_MENUNGGU_VERIFIKASI,
                'paid_amount' => $attributes['total_amount'],
                'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'tracking_number' => null,
            ];
        });
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Order::STATUS_COMPLETED,
                'paid_amount' => $attributes['total_amount'],
                'paid_at' => fake()->dateTimeBetween('-30 days', '-5 days'),
                'tracking_number' => fake()->regexify('[A-Z]{2}[0-9]{9,12}'),
            ];
        });
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Order::STATUS_CANCELLED,
                'paid_amount' => fake()->boolean(50) ? fake()->numberBetween(0, $attributes['total_amount']) : 0,
                'paid_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-30 days', 'now') : null,
                'tracking_number' => null,
            ];
        });
    }

}

