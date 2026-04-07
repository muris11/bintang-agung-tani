<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_SUCCESS = 'success';

    const STATUS_FAILED = 'failed';

    const STATUS_EXPIRED = 'expired';

    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method',
        'provider',
        'provider_transaction_id',
        'provider_status',
        'amount',
        'status',
        'payment_data',
        'notes',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function markAsSuccess(): void
    {
        $this->status = self::STATUS_SUCCESS;
        $this->paid_at = now();
        $this->save();
    }

    public function markAsFailed(): void
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
    }

    public function markAsExpired(): void
    {
        $this->status = self::STATUS_EXPIRED;
        $this->save();
    }

    public function getFormattedAmount(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }
}
