<?php

namespace App\Policies\Concerns;

use App\Enums\Role;
use App\Models\User;

trait AdminBypass
{
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        if ($user->role === Role::ADMIN) {
            return true;
        }

        return null;
    }
}
