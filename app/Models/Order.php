<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';

    const STATUS_PAYMENT_PENDING = 'payment_pending';

    const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    const STATUS_PROCESSING = 'processing';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_REFUNDED = 'refunded';

    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_PAYMENT_PENDING => 'Menunggu Pembayaran',
        self::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
        self::STATUS_PROCESSING => 'Diproses',
        self::STATUS_SHIPPED => 'Dikirim',
        self::STATUS_DELIVERED => 'Terkirim',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
        self::STATUS_REFUNDED => 'Dikembalikan',
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING => 'yellow',
        self::STATUS_PAYMENT_PENDING => 'orange',
        self::STATUS_MENUNGGU_VERIFIKASI => 'orange',
        self::STATUS_PROCESSING => 'blue',
        self::STATUS_SHIPPED => 'indigo',
        self::STATUS_DELIVERED => 'purple',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_CANCELLED => 'red',
        self::STATUS_REFUNDED => 'gray',
    ];

    protected $fillable = [
        'user_id',
        'address_id',
        'payment_method_id',
        'order_number',
        'status',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'paid_amount',
        'qr_code_path',
        'qr_code_data',
        'shipping_courier',
        'shipping_service',
        'tracking_number',
        'shipping_address_snapshot',
        'shipping_phone',
        'payment_method',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });

        static::created(function ($order) {
            $order->statusHistories()->create([
                'status' => $order->status,
                'previous_status' => null,
                'notes' => 'Pesanan dibuat',
            ]);
        });

        static::updating(function ($order) {
            if ($order->isDirty('status')) {
                $order->statusHistories()->create([
                    'status' => $order->status,
                    'previous_status' => $order->getOriginal('status'),
                    'notes' => 'Status diubah',
                ]);

                $timestampField = match ($order->status) {
                    self::STATUS_SHIPPED => 'shipped_at',
                    self::STATUS_DELIVERED => 'delivered_at',
                    self::STATUS_CANCELLED => 'cancelled_at',
                    default => null,
                };

                if ($timestampField && empty($order->$timestampField)) {
                    $order->$timestampField = now();
                }
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'BAT';
        $date = now()->format('Ymd');
        $maxAttempts = 5;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $random = strtoupper(Str::random(4));
            $orderNumber = "{$prefix}-{$date}-{$random}";

            // Check if order number already exists
            if (! self::where('order_number', $orderNumber)->exists()) {
                return $orderNumber;
            }
        }

        // Fallback with timestamp and more random characters if collisions persist
        $timestamp = now()->format('His');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$date}-{$timestamp}-{$random}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'entity_id')
            ->where('entity_type', self::class)
            ->orderBy('created_at', 'desc');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function latestPaymentProof(): HasOne
    {
        return $this->hasOne(PaymentProof::class)->latestOfMany('created_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaymentPending($query)
    {
        return $query->where('status', self::STATUS_PAYMENT_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_REFUNDED]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function getStatusBadgeClass(): string
    {
        $color = $this->getStatusColor();

        return match ($color) {
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'green' => 'bg-green-100 text-green-800',
            'red' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function isPaymentPending(): bool
    {
        return $this->status === self::STATUS_PAYMENT_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipped(): bool
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_VERIFIKASI;
    }

    /**
     * Check if order has been verified by admin (ready for pickup)
     * Returns true for processing, shipped, delivered, and completed statuses
     */
    public function isVerified(): bool
    {
        return in_array($this->status, [
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Check if order has payment proof uploaded
     */
    public function hasPaymentProof(): bool
    {
        return $this->paymentProofs()->exists();
    }

    /**
     * Check if user can view the barcode/QR code
     * Barcode is only visible after admin verification (processing status or later)
     */
    public function canViewBarcode(): bool
    {
        return $this->isVerified();
    }

    public function getQrCodeUrl(): ?string
    {
        if (empty($this->qr_code_path)) {
            return null;
        }

        return Storage::url($this->qr_code_path);
    }

    public function generateQrCodeData(): string
    {
        return json_encode([
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'total' => $this->total_amount,
            'timestamp' => now()->timestamp,
        ]);
    }

    public function canUploadProof(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PAYMENT_PENDING,
            self::STATUS_MENUNGGU_VERIFIKASI,
        ]);
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_MENUNGGU_VERIFIKASI,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function getPaymentStatusLabel(): string
    {
        if (! $this->isPaid()) {
            return 'Belum Dibayar';
        }

        if ($this->isMenungguVerifikasi()) {
            return 'Menunggu Verifikasi';
        }

        return 'Lunas / Terverifikasi';
    }

    public function getPaymentStatusClass(): string
    {
        if (! $this->isPaid()) {
            return 'bg-red-100 text-red-700 border border-red-200';
        }

        if ($this->isMenungguVerifikasi()) {
            return 'bg-orange-100 text-orange-700 border border-orange-200';
        }

        return 'bg-green-100 text-green-700 border border-green-200';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAYMENT_PENDING]);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAYMENT_PENDING]) && ! $this->isPaid();
    }

    public function getRemainingAmount(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getFormattedTotal(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedSubtotal(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingCost(): string
    {
        return 'Rp '.number_format($this->shipping_cost, 0, ',', '.');
    }

    public function getFormattedPaidAmount(): string
    {
        return 'Rp '.number_format($this->paid_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAmount(): string
    {
        return 'Rp '.number_format($this->getRemainingAmount(), 0, ',', '.');
    }

    public function updateStatus(string $status, ?string $notes = null, ?int $changedBy = null): void
    {
        $previousStatus = $this->status;
        $this->status = $status;
        $this->save();

        $this->statusHistories()->create([
            'status' => $status,
            'previous_status' => $previousStatus,
            'notes' => $notes,
            'changed_by' => $changedBy,
        ]);
    }

    public function markAsPaid(float $amount, string $paymentMethod, ?int $changedBy = null): void
    {
        $this->paid_amount += $amount;
        $this->payment_method = $paymentMethod;

        if ($this->isPaid()) {
            $this->paid_at = now();
        }

        $this->save();

        $this->statusHistories()->create([
            'status' => $this->status,
            'previous_status' => $this->status,
            'notes' => "Pembayaran diterima: {$this->getFormattedPaidAmount()} via {$paymentMethod}",
            'changed_by' => $changedBy,
        ]);
    }

    public function getTimeline(): array
    {
        $timeline = [];

        $timeline[] = [
            'status' => 'created',
            'label' => 'Pesanan Dibuat',
            'timestamp' => $this->created_at,
            'completed' => true,
        ];

        $steps = [
            ['status' => self::STATUS_PAYMENT_PENDING, 'label' => 'Menunggu Pembayaran', 'timestamp' => null],
            ['status' => self::STATUS_PROCESSING, 'label' => 'Diproses', 'timestamp' => null],
            ['status' => self::STATUS_SHIPPED, 'label' => 'Dikirim', 'timestamp' => $this->shipped_at],
            ['status' => self::STATUS_DELIVERED, 'label' => 'Terkirim', 'timestamp' => $this->delivered_at],
            ['status' => self::STATUS_COMPLETED, 'label' => 'Selesai', 'timestamp' => null],
        ];

        foreach ($steps as $step) {
            if ($step['timestamp']) {
                $timeline[] = [
                    'status' => $step['status'],
                    'label' => $step['label'],
                    'timestamp' => $step['timestamp'],
                    'completed' => true,
                ];
            } elseif (in_array($this->status, [$step['status'], self::STATUS_COMPLETED])) {
                $timeline[] = [
                    'status' => $step['status'],
                    'label' => $step['label'],
                    'timestamp' => null,
                    'completed' => false,
                ];
            }
        }

        if ($this->isCancelled()) {
            $timeline[] = [
                'status' => self::STATUS_CANCELLED,
                'label' => 'Dibatalkan',
                'timestamp' => $this->cancelled_at,
                'completed' => true,
            ];
        }

        return $timeline;
    }
}
