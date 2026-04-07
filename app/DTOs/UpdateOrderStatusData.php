<?php

namespace App\DTOs;

final class UpdateOrderStatusData
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $notes,
        public readonly ?int $changedBy,
    ) {}

    /**
     * Create from validated request data
     */
    public static function fromRequest(array $data, ?int $changedBy = null): self
    {
        return new self(
            status: $data['status'],
            notes: $data['notes'] ?? null,
            changedBy: $changedBy,
        );
    }
}
