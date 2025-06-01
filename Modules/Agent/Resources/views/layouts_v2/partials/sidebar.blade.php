<li class="nav-item {{ in_array($request->segment(1), ['agents']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#agents-menu"
        aria-expanded="true" aria-controls="agents-menu">
        <i class="ti-layout-media-right-alt"></i>
        <span>@lang('agent::lang.agent')</span>
    </a>
    <div id="agents-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('agent::lang.agent'):</h6>
            <a class="collapse-item {{ $request->segment(1) == 'agent' && $request->segment(2) == 'dashboard'? 'active' : '' }}" href="{{action('\Modules\Agent\Http\Controllers\AgentController@dashboard')}}">@lang('agent::lang.dashboard')</a>
        </div>
    </div>
</li>