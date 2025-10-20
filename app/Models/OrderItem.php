<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','product_id',
        'product_name_snapshot','sku_snapshot','weight_snapshot',
        'qty','price_each','line_total',
    ];

    protected $casts = [
        'weight_snapshot' => 'integer',
        'qty'             => 'integer',
        'price_each'      => 'integer',
        'line_total'      => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function (OrderItem $item) {
            $item->line_total = (int) $item->price_each * (int) $item->qty;
        });
    }

    // ===== Relationships =====
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
