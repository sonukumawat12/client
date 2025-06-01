<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AutoLogoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $autoLogoutTime = (60*15); // Auto log off time in seconds (15 minutes)
        if (Auth::check()) {
            $lastActivity = Session::get('lastActivityTime');
            if ($lastActivity !== null && time() - $lastActivity > $autoLogoutTime) {
                Auth::logout();
                Session::flush(); // Remove all session data
                return redirect('/login')->with('warning', 'You have been automatically logged out due to inactivity.');
            }
            Session::put('lastActivityTime', time());
        }
        return $next($request);
    }
}
