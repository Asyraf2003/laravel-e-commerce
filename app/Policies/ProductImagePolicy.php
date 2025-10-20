<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductImage;
use App\Policies\Concerns\AdminBypass;
use App\Models\CartItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductImagePolicy
{
    use AdminBypass, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, ProductImage $image): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ProductImage $image): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ProductImage $image): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, ProductImage $image): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, ProductImage $image): bool
    {
        return $user->isAdmin();
    }
}
