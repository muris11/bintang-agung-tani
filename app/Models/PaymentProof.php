<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PaymentProof extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_VERIFIED = 'verified';

    const STATUS_REJECTED = 'rejected';

    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu Verifikasi',
        self::STATUS_VERIFIED => 'Terverifikasi',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING => 'yellow',
        self::STATUS_VERIFIED => 'green',
        self::STATUS_REJECTED => 'red',
    ];

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method_id',
        'image_path',
        'original_filename',
        'file_size',
        'notes',
        'status',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function getImageUrl(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        return Storage::url($this->image_path);
    }

    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function markAsVerified(int $verifiedBy, ?string $adminNotes = null): void
    {
        $this->status = self::STATUS_VERIFIED;
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->admin_notes = $adminNotes;
        $this->save();
    }

    public function markAsRejected(int $verifiedBy, ?string $adminNotes = null): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->admin_notes = $adminNotes;
        $this->save();
    }
}
