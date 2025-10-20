<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CartItem;
use App\Policies\Concerns\AdminBypass;

class CartItemPolicy
{
    use AdminBypass; // assumed: before() returns true if $user->isAdmin()

    /** Untuk route: can:viewAny,App\Models\CartItem */
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active;
    }

    public function view(User $user, CartItem $item): bool
    {
        return $item->user_id === $user->id;
    }

    /** Untuk add ke cart: can:create,App\Models\CartItem */
    public function create(User $user): bool
    {
        return (bool) $user->is_active;
    }

    /** Update qty (hanya saat item masih di keranjang) */
    public function update(User $user, CartItem $item): bool
    {
        return $item->user_id === $user->id
            && $item->status === CartItem::STATUS_IN_CART;
    }

    /** Hapus dari keranjang (soft change ke removed) */
    public function delete(User $user, CartItem $item): bool
    {
        return $item->user_id === $user->id
            && $item->status === CartItem::STATUS_IN_CART;
    }

    /** Tidak ada restore() karena kita tidak pakai 'saved' */
}
