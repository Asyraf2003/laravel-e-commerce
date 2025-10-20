<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class OtherOnly
{
    public function handle(Request $request, Closure $next)
    {
        $role = $request->user()?->role;
        $roleValue = $role instanceof Role ? $role->value : (string) $role;

        if ($roleValue === 'other') {
            return $next($request);
        }

        abort(403);
    }
}
