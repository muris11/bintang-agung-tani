<?php

namespace App\DTOs;

final class CreateOrderData
{
    public function __construct(
        public readonly int $userId,
        public readonly ?int $addressId,
        public readonly ?string $shippingAddress,
        public readonly ?string $shippingPhone,
        public readonly float $shippingCost,
        public readonly ?string $shippingCourier,
        public readonly ?string $shippingService,
        public readonly ?string $notes,
    ) {}

    /**
     * Create from validated request data
     */
    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            userId: $userId,
            addressId: $data['address_id'] ?? null,
            shippingAddress: $data['shipping_address'] ?? null,
            shippingPhone: $data['shipping_phone'] ?? null,
            shippingCost: (float) ($data['shipping_cost'] ?? 0),
            shippingCourier: $data['shipping_courier'] ?? null,
            shippingService: $data['shipping_service'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * Convert to array for model creation
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'address_id' => $this->addressId,
            'shipping_address' => $this->shippingAddress,
            'shipping_phone' => $this->shippingPhone,
            'shipping_cost' => $this->shippingCost,
            'shipping_courier' => $this->shippingCourier,
            'shipping_service' => $this->shippingService,
            'notes' => $this->notes,
        ];
    }
}
