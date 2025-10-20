<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id','session_id','product_id'];

    protected $casts = [
        'user_id'    => 'integer',
        'product_id' => 'integer',
        'session_id' => 'string',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function product(){ return $this->belongsTo(Product::class); }

    public function scopeForIdentity($q, ?int $userId, ?string $sessionId)
    {
        return $userId ? $q->where('user_id', $userId)
                       : $q->where('session_id', $sessionId);
    }

    public function scopeOwned($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }
}


