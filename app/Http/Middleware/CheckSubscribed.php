<?php

namespace App\Http\Middleware;

use Closure;
use App\Business;
use Illuminate\Support\Facades\Auth;
use Modules\Superadmin\Entities\Subscription;

class CheckSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {   
        $business_id = request()->session()->get('user.business_id');
        $business_exist  = Business::where('id', $business_id)->first();
        
        if(empty($business_id) || empty($business_exist)){
            return redirect('/logout');
        }
        
        
        $active_sub = Subscription::current_subscription($business_id);
        
        
        if (!empty($active_sub)) {
            $package_details = $active_sub->package_details;
            
            if(empty($package_details[$module]) && !empty($package_details['ns_'.$module])){
                return redirect('home/not-subscribed');
            }else{
                return $next($request);
            }
        }else{
            return $next($request);
        }

        
    }
}
