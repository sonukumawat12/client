 <li class="nav-item {{ in_array($request->segment(1), ['fleet-management']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#fleetmanagement-menu"
        aria-expanded="true" aria-controls="fleetmanagement-menu">
        <i class="fa fa-car"></i>
        <span>@lang('distribution::lang.fleet_management')</span>
    </a>
    <div id="fleetmanagement-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('distribution::lang.fleet_management'):</h6>
            <a class="collapse-item {{ $request->segment(2) == 'fleet' ? 'active' : '' }}" href="{{action('\Modules\Distribution\Http\Controllers\DistributionController@index')}}">@lang('distribution::lang.list_fleet')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'route-operation' ? 'active' : '' }}" href="{{action('\Modules\Distribution\Http\Controllers\RouteOperationController@index')}}">@lang('distribution::lang.route_operation')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\Distribution\Http\Controllers\SettingController@index')}}"> @lang('distribution::lang.fleet_settings')</a>
        </div>
    </div>
</li>