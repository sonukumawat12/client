<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if ( ! Auth::user()->isAdmin()) { 
                \abort(404);
            }
            
            disableInDemo();

            if( ! Auth::user()->can('admin_only') ){
                \abort(404);
            }
            
        }

        return $next($request);
    }
}
