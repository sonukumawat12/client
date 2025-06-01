<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class SetUserLocale
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
        $locale =  setting('default_lang', 'en');

        if( isAppInstalled() ){
            
            if( Auth::guard($guard)->check() ){
                $userLocale = Auth::user()->locale;
                if($userLocale){
                   $locale = $userLocale;
                } 
            } 
            
            app()->setLocale($locale);
            url()->defaults(['locale' => $locale]);
            date_default_timezone_set(setting('timezone', 'UTC'));
            
        }

        return $next($request);
    }
}
