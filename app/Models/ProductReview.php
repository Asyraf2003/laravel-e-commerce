<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class ProductReview extends Model
{
    use SoftDeletes, HasFactory;

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'product_id','user_id','order_item_id',
        'rating','title','body',
        'is_verified_purchase','status',
        'approved_at','rejected_at','rejected_reason',
        'helpful_count','report_count',
    ];

    protected $casts = [
        'rating'              => 'integer',
        'is_verified_purchase'=> 'boolean',
        'approved_at'         => 'datetime',
        'rejected_at'         => 'datetime',
        'helpful_count'       => 'integer',
        'report_count'        => 'integer',
    ];

    // ===== Relationships =====
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class, 'review_id');
    }

    // ===== Scopes =====
    public function scopeApproved($q)
    {
        return $q->where('status', self::STATUS_APPROVED);
    }

    // ===== Helpers =====
    public function markApproved(): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_at = now();
        $this->rejected_at = null;
        $this->save();
    }

    public function markRejected(?string $reason = null): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejected_at = now();
        $this->approved_at = null;
        $this->rejected_reason = $reason;
        $this->save();
    }

    public function ensureVerifiedPurchase(): void
    {
        // Set true jika order_item_id valid dan mengacu ke product yang sama
        if ($this->order_item_id && $this->orderItem && $this->orderItem->product_id === $this->product_id) {
            $this->is_verified_purchase = true;
            $this->save();
        }
    }
}
