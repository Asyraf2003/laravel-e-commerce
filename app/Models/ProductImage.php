<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class ProductImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'image_path', 'is_primary',
        'sort_order', 'alt_text',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ===== Relationships =====
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ===== Scopes =====
    public function scopePrimary($q)
    {
        return $q->where('is_primary', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order');
    }
}
