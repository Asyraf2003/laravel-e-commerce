<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;
use App\Policies\Concerns\AdminBypass;

class WishlistPolicy
{
    use AdminBypass;

    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active; 
    }

    public function view(User $user, Wishlist $item): bool
    {
        return $user->isAdmin() || $item->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_active; 
    }

    public function update(User $user, Wishlist $item): bool
    {
        return $user->isAdmin() || $item->user_id === $user->id;
    }

    public function delete(User $user, Wishlist $item): bool
    {
        return $user->isAdmin() || $item->user_id === $user->id;
    }

    public function restore(User $user, Wishlist $item): bool
    {
        return $user->isAdmin() || $item->user_id === $user->id;
    }

    public function forceDelete(User $user, Wishlist $item): bool
    {
        return $user->isAdmin();
    }
}
