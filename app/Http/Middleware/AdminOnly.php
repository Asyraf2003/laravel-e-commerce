<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $role = $request->user()?->role;
        $roleValue = $role instanceof Role ? $role->value : (string) $role;

        if ($roleValue === 'admin') {
            return $next($request);
        }

        abort(403);
    }
}
