<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'full_address',
        'province',
        'city',
        'district',
        'postal_code',
        'is_default',
        'notes',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $appends = ['complete_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getCompleteAddressAttribute(): string
    {
        $parts = [
            $this->full_address,
            $this->district,
            $this->city,
            $this->province,
        ];

        if ($this->postal_code) {
            $parts[] = $this->postal_code;
        }

        return implode(', ', array_filter($parts));
    }
}
