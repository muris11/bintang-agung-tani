<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'status',
        'previous_status',
        'notes',
        'changed_by',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getStatusLabel(): string
    {
        return Order::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getPreviousStatusLabel(): ?string
    {
        if (! $this->previous_status) {
            return null;
        }

        return Order::STATUS_LABELS[$this->previous_status] ?? $this->previous_status;
    }
}
