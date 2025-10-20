<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $role = $user->role;
        $roleValue = $role instanceof Role ? $role->value : (string) $role;

        if (empty($roles) || in_array($roleValue, $roles, true)) {
            return $next($request);
        }

        abort(403);
    }
}
