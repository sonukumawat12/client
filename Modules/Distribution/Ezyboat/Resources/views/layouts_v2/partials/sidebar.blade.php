<li class="nav-item {{ in_array($request->segment(1), ['ezyboat']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#eazyboat-menu"
        aria-expanded="true" aria-controls="eazyboat-menu">
        <i class="ti-world"></i>
        <span>@lang('ezyboat::lang.ezyboat')</span>
    </a>
    <div id="eazyboat-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('ezyboat::lang.ezyboat'):</h6>
            <a class="collapse-item {{ $request->segment(2) == 'list' ? 'active' : '' }}" href="{{action('\Modules\Ezyboat\Http\Controllers\EzyboatController@index')}}">@lang('ezyboat::lang.list_boats')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'boat-operation' ? 'active' : '' }}" href="{{action('\Modules\Ezyboat\Http\Controllers\BoatOperationController@index')}}">@lang('ezyboat::lang.list_boat_trips')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active' : '' }}" href="{{action('\Modules\Ezyboat\Http\Controllers\SettingController@index')}}">@lang('ezyboat::lang.fleet_settings')</a>
        </div>
    </div>
</li>
