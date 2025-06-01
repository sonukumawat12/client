{{-- @if($__is_mfg_enabled) --}}
	<li class="nav-item {{ in_array($request->segment(1), ['manufacturing']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#manufacturing-menu"
            aria-expanded="true" aria-controls="manufacturing-menu">
            <i class="fa fa-industry"></i>
            <span>@lang('manufacturing::lang.manufacturing')</span>
        </a>
        <div id="manufacturing-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('manufacturing::lang.manufacturing'):</h6>
                <a class="collapse-item {{ $request->segment(1) == 'manufacturing'? 'active active-sub' : '' }}" href="{{action('\Modules\Manufacturing\Http\Controllers\ManufacturingController@index')}}">@lang('manufacturing::lang.manufacturing')</a>
            </div>
        </div>
    </li>
