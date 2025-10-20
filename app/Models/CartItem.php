<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    public const STATUS_IN_CART    = 'in_cart';
    public const STATUS_CHECKEDOUT = 'checked_out';
    public const STATUS_SAVED      = 'saved';
    public const STATUS_REMOVED    = 'removed';

    protected $fillable = [
        'user_id','session_id','product_id','order_id',
        'qty','price_each',
        'product_name_snapshot','sku_snapshot','weight_snapshot',
        'status',
    ];

    protected $casts = [
        'qty'             => 'integer',
        'price_each'      => 'integer',
        'weight_snapshot' => 'integer',
    ];

    // ===== Relationships =====
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ===== Accessors =====
    public function getLineTotalAttribute(): int
    {
        return (int) $this->price_each * (int) $this->qty;
    }

    // ===== Scopes =====
    public function scopeInCart($q)
    {
        return $q->where('status', self::STATUS_IN_CART);
    }

    public function scopeSaved($q)
    {
        return $q->where('status', self::STATUS_SAVED);
    }

    public function scopeRemoved($q)
    {
        return $q->where('status', self::STATUS_REMOVED);
    }
}
