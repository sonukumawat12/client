<?php

namespace Modules\HelpGuide\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Frontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
      $isFrontendEnabled =  setting('frontend_enabled', true);

      if( ! $isFrontendEnabled ) {
        if ( Auth::guard($guard)->check() && Auth::user()->isAdmin() ) {
          return $next($request);
        }
        
        return redirect(route('login'));
      }

      return $next($request);
    }
}
