<li class="nav-item {{ in_array($request->segment(1), ['dsr']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#dsr-menu"
        aria-expanded="true" aria-controls="dsr-menu">
        <i class="fa fa-users"></i>
        <span>@lang('dsr::lang.dsr_management')</span>
    </a>
    <div id="dsr-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Dsr Management:</h6>
                <a class="collapse-item {{ $request->segment(1) == 'dsr' && $request->segment(2) == 'Settings' ? 'active' : '' }}" href="{{action('\Modules\Dsr\Http\Controllers\DsrController@index')}}">@lang('dsr::lang.settings')</a>
                <a class="collapse-item {{ $request->segment(1) == 'dsr' && $request->segment(2) == 'report' ? 'active' : '' }}" href="{{action('\Modules\Dsr\Http\Controllers\DsrController@index')}}">@lang('dsr::lang.report')</a>
        </div>
    </div>
</li>
