<?php

namespace Modules\Dsr\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();

        // Ensure the user has the specified role within the context of the tenant
        if ($user && $user->hasRole($role)) {
            return $next($request);
        }

        throw UnauthorizedException::forRoles([$role]);
    }
}
