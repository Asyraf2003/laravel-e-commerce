<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AdminBypass;

class UserPolicy
{
    use AdminBypass;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    public function changeRole(User $user, User $target): bool
    {
        return $user->isAdmin() && $user->id !== $target->id;
    }
}
