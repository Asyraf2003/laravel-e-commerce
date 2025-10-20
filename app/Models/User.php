<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\CartItem;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
            'role'              => Role::class, 
        ];
    }
    
    public function roleString(): string
    {
        return $this->role instanceof Role ? $this->role->value : (string) $this->role;
    }
    
    public function getRoleValueAttribute(): string
    {
        return $this->roleString();
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    public function isOther(): bool
    {
        return $this->role === Role::OTHER;
    }

    public function isUser(): bool
    {
        return $this->role === Role::USER;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
