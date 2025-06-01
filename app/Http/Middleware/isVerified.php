<?php

namespace App\Http\Middleware;

use Closure;
use App\UserSetting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()){
            $setting = UserSetting::where('user_id',auth()->user()->id)->first();
            $is_super_admin_login = request()->session()->get('superadmin-logged-in');
           if(!is_null($is_super_admin_login) && $is_super_admin_login  == 1){
               //skip
           }elseif ($setting && $setting->opt_verification_enabled && !$setting->verification_done) {
                return redirect('/login');
            }
        }
        
        return $next($request);
                
        
    }
}
