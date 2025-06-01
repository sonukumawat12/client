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


 <li class="nav-item {{ in_array($request->segment(1), ['fleet-management']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#fleetmanagement-menu"
        aria-expanded="true" aria-controls="fleetmanagement-menu">
        <i class="fa fa-car"></i>
        <span>@lang('fleet::lang.fleet_management')</span>
    </a>
    <div id="fleetmanagement-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('fleet::lang.fleet_management'):</h6>
            
            @if(!empty($pacakge_details['add_trip_operations']))
                <a class="collapse-item {{ $request->segment(2) == 'route-operation' && $request->segment(3) == 'create' ? 'active' : '' }}" href="{{ action('\Modules\Fleet\Http\Controllers\RouteOperationController@create') }}">
                    @lang('fleet::lang.fleet_add_trip_operation')
                </a>
            @endif
            
            @if(!empty($pacakge_details['list_fleet']))
                <a class="collapse-item {{ $request->segment(2) == 'fleet' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\FleetController@index')}}">@lang('fleet::lang.list_fleet')</a>
            @endif
            
            @if(!empty($pacakge_details['milage_changes']))
                <a class="collapse-item {{ $request->segment(2) == 'milage-changes' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@milage_changes')}}">@lang('fleet::lang.milage_changes')</a>
            @endif
            
            @if(!empty($pacakge_details['list_trip_operations']))
                <a class="collapse-item {{ $request->segment(2) == 'route-operation'&& $request->segment(3) == '' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@index')}}">@lang('fleet::lang.route_operation')</a>
            @endif
            
            @if(!empty($pacakge_details['fleet_invoices']))
                <a class="collapse-item {{ $request->segment(2) == 'create-fleet-invoices' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@index_create')}}">@lang('fleet::lang.fleet_invoices')</a>
            @endif
            
            @if(!empty($pacakge_details['fuel_management']))
                <a class="collapse-item {{ $request->segment(2) == 'fuel_management' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\FleetController@fuelManagement')}}"> @lang('fleet::lang.fuel_management')</a>
            @endif
            
            
            @if(!empty($pacakge_details['fleet_settings']))
                <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\SettingController@index')}}"> @lang('fleet::lang.fleet_settings')</a>
            @endif
            
            @if(!empty($pacakge_details['fleet_p_l']))
                <a class="collapse-item {{ $request->segment(2) == 'fleet-profit-loss' ? 'active' : '' }}" href="{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@fleet_profit_loss')}}"> @lang('fleet::lang.fleet_profit_loss')</a>
            @endif
            
            <!--<a class="collapse-item "  href="{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@create')}}">@lang('fleet::lang.fleet_add_trip_operation')</a>-->
        </div>
    </div>
</li>
