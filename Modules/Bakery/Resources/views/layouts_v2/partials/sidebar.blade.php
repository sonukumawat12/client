@php
   
   $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp

@if(!empty($pacakge_details['bakery_module']) && auth()->user()->can('bakery_login'))
<li class="nav-item {{ in_array($request->segment(1), ['bakery']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bakery-menu"
        aria-expanded="true" aria-controls="bakery-menu">
        <i class="fa fa-calculator"></i>
        <span>@lang('superadmin::lang.bakery_module')</span>
    </a>
    <div id="bakery-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('superadmin::lang.bakery_module'):</h6>
            @if(!empty($pacakge_details['bakery_module']))
                <a class="collapse-item {{ $request->segment(1) == 'list' ? 'active' : '' }}" href="{{action('\Modules\Bakery\Http\Controllers\BakeryController@settings')}}">@lang('bakery::lang.bakery_settings')</a>
            @endif
            
            <a class="collapse-item {{ $request->segment(1) == 'list' ? 'active' : '' }}" href="{{action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@index')}}">@lang('bakery::lang.loading')</a>
            <a class="collapse-item {{ $request->segment(1) == 'activity-log' ? 'active' : '' }}" href="{{action('\Modules\Bakery\Http\Controllers\BakeryController@getUserActivityReport')}}">@lang('bakery::lang.user_activities')</a>
            
        </div>
    </div>
</li>


{{--<li class="nav-item {{ in_array($request->segment(2), ['loading']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#loading-menu"
        aria-expanded="true" aria-controls="loading-menu">
        <i class="fa fa-calculator"></i>
        <span>@lang('superadmin::lang.loading')</span>
    </a>
    <div id="loading-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('superadmin::lang.loading'):</h6>
                <a class="collapse-item {{ $request->segment(2) == 'list' ? 'active' : '' }}" href="{{action('\Modules\Bakery\Http\Controllers\LoadingController@settings')}}">@lang('superadmin::lang.loading')</a>

        </div>
    </div>
</li> --}}



@endif

