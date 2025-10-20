<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'category_id',
        'name','slug','sku',
        'short_desc','long_desc',
        'original_price','discount_percent',
        'reviews_count','reviews_avg',
        'stock','weight',
        'share_fb','share_x','share_wa',
        'is_best_seller','is_new','is_hot','is_featured',
        'is_active','published_at',
    ];

    protected $casts = [
        'original_price'  => 'integer',
        'discount_percent'=> 'integer',
        'reviews_count'   => 'integer',
        'reviews_avg'     => 'decimal:2',
        'stock'           => 'integer',
        'weight'          => 'integer',
        'share_fb'        => 'boolean',
        'share_x'         => 'boolean',
        'share_wa'        => 'boolean',
        'is_best_seller'  => 'boolean',
        'is_new'          => 'boolean',
        'is_hot'          => 'boolean',
        'is_featured'     => 'boolean',
        'is_active'       => 'boolean',
        'published_at'    => 'datetime',
    ];

    // tampilkan otomatis saat toArray()/JSON
    protected $appends = [
        'has_discount',
        'final_price',
        'saved_amount',
        'original_price_formatted',
        'final_price_formatted',
        'discount_price_formatted', // alias final_price_formatted
        'saved_amount_formatted',
    ];

    // ===== Relationships =====
    public function category()      { return $this->belongsTo(Category::class); }
    public function images()        { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function primaryImage()  { return $this->hasOne(ProductImage::class)->where('is_primary', true); }
    public function wishlists()     { return $this->hasMany(Wishlist::class); }
    public function orderItems()    { return $this->hasMany(OrderItem::class); }
    public function cartItems()     { return $this->hasMany(CartItem::class); }
    public function reviews()       { return $this->hasMany(ProductReview::class); }

    // ===== Scopes =====
    public function scopeActive($q)
    {
        return $q->where('is_active', true)
                 ->where(function ($qq) {
                     $qq->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                 });
    }

    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    // ===== Accessors (computed) =====

    // clamp 0..100 saat baca
    public function getDiscountPercentAttribute($value): int
    {
        $p = (int) ($value ?? 0);
        return max(0, min(100, $p));
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percent > 0 && (int) $this->original_price > 0;
    }

    public function getFinalPriceAttribute(): int
    {
        $price = (int) ($this->original_price ?? 0);
        if ($this->has_discount) {
            return (int) round($price * (100 - $this->discount_percent) / 100);
        }
        return $price;
    }

    // alias untuk kompatibilitas Blade lama ($product->discount_price_formatted)
    public function getDiscountPriceAttribute(): int
    {
        return $this->final_price;
    }

    public function getSavedAmountAttribute(): int
    {
        return max(0, (int) ($this->original_price ?? 0) - (int) $this->final_price);
    }

    // ===== Formatting helpers =====
    protected function money(int $amount): string
    {
        // pakai intl kalau tersedia; fallback ke number_format
        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
            return $fmt->formatCurrency($amount, 'IDR');
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function getOriginalPriceFormattedAttribute(): string
    {
        return $this->money((int) ($this->original_price ?? 0));
    }

    public function getFinalPriceFormattedAttribute(): string
    {
        return $this->money((int) $this->final_price);
    }

    // kompatibel dengan Blade lama
    public function getDiscountPriceFormattedAttribute(): string
    {
        return $this->final_price_formatted;
    }

    public function getSavedAmountFormattedAttribute(): string
    {
        return $this->money((int) $this->saved_amount);
    }
}
