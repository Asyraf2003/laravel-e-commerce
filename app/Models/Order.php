<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Status standar
    public const STATUS_DRAFT           = 'draft';
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID            = 'paid';
    public const STATUS_CANCELLED       = 'cancelled';
    public const STATUS_SHIPPED         = 'shipped';
    public const STATUS_DELIVERED       = 'delivered';

    protected $fillable = [
        'user_id','order_no',
        'recipient_name','recipient_phone',
        'province_name','city_name','subdistrict_name',
        'destination_level',
        'address','postal_code',
        'courier','service','shipping_cost','weight_total','discount_total',
        'tax_total','subtotal','total','currency',
        'payment_gateway','','transaction_status','fraud_status',
        'gross_amount','','midtrans_order_id','payment_token',
        'payment_redirect_url','midtrans_payload',
        'status','placed_at','paid_at',
    ];

    protected $casts = [
        'midtrans_payload' => 'array',
        'placed_at'        => 'datetime',
        'paid_at'          => 'datetime',
        'shipping_cost'    => 'integer',
        'weight_total'     => 'integer',
        'discount_total'   => 'integer',
        'tax_total'        => 'integer',
        'subtotal'         => 'integer',
        'total'            => 'integer',
        'gross_amount'     => 'integer',
    ];

    // ===== Relationships =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ===== Helpers =====
    public function getComputedWeightTotalAttribute(): int
    {
        return (int) $this->items->sum(fn($i) => ($i->weight_snapshot ?? 0) * (int) $i->qty);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    // Midtrans mapping helper
    public function applyMidtransStatus(string $transactionStatus, ?string $fraudStatus = null): void
    {
        $this->transaction_status = $transactionStatus;
        $this->fraud_status = $fraudStatus;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $this->status = self::STATUS_PAID;
                $this->paid_at = now();
            } else {
                $this->status = self::STATUS_PENDING_PAYMENT;
            }
        } elseif ($transactionStatus === 'settlement') {
            $this->status = self::STATUS_PAID;
            $this->paid_at = now();
        } elseif (in_array($transactionStatus, ['deny','cancel','expire'], true)) {
            $this->status = self::STATUS_CANCELLED;
            $this->cancelled_at = now();
        } else {
            $this->status = self::STATUS_PENDING_PAYMENT;
        }
    }

    // ===== Scopes =====
    public function scopeRecent($q)
    {
        return $q->orderByDesc('created_at');
    }

    public function scopeForUser($q, $userId)
    {
        return $q->where('user_id', $userId);
    }
}
