<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use App\Policies\Concerns\AdminBypass;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use AdminBypass, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id; 
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin() || ($order->status === Order::STATUS_DRAFT && $order->user_id === $user->id);
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $order->user_id === $user->id
            && in_array($order->status, [Order::STATUS_DRAFT, Order::STATUS_PENDING_PAYMENT], true);
    }
}
