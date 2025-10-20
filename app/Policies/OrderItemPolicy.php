<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrderItem;
use App\Policies\Concerns\AdminBypass;

class OrderItemPolicy
{
    use AdminBypass;

    public function view(User $user, OrderItem $item): bool
    {
        return $user->isAdmin() || $item->order?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, OrderItem $item): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, OrderItem $item): bool
    {
        return $user->isAdmin();
    }
}
