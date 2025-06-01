<li class="nav-item {{ in_array($request->segment(1), ['leads']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#leads-menu"
        aria-expanded="true" aria-controls="leads-menu">
        <i class="fa fa-filter"></i>
        <span>@lang('leads::lang.leads')</span>
    </a>
    <div id="leads-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('leads::lang.leads'):</h6>
            
            @if($leads)
            @if(auth()->user()->can('leads.view') || auth()->user()->can('leads.edit') ||
            auth()->user()->can('leads.destory')|| auth()->user()->can('leads.create'))
                <a class="collapse-item {{ $request->segment(1) == 'leads' && $request->segment(2) == 'leads'? 'active' : '' }}" href="{{action('\Modules\Leads\Http\Controllers\LeadsController@index')}}">@lang('leads::lang.leads')</a>
            @endcan
            @endif
            @if($leads_import)
            @can('leads.import')
                <a class="collapse-item {{ $request->segment(1) == 'leads' && $request->segment(2) == 'import'? 'active' : '' }}" href="{{action('\Modules\Leads\Http\Controllers\ImportLeadsController@index')}}">@lang('leads::lang.import_data')</a>
            @endcan
            @endif
            @if($day_count)
            @can('day_count')
                <a class="collapse-item {{ $request->segment(1) == 'leads' && $request->segment(2) == 'day-count'? 'active' : '' }}" href="{{action('\Modules\Leads\Http\Controllers\DayCountController@index')}}">@lang('leads::lang.day_count')</a>
            @endcan
            @endif
            
            @if($leads_settings)
            @can('leads.settings')
                <a class="collapse-item {{ $request->segment(1) == 'leads' && $request->segment(2) == 'settings'? 'active' : '' }}" href="{{action('\Modules\Leads\Http\Controllers\SettingController@index')}}">@lang('leads::lang.settings')</a>
            @endcan
            @endif
                
        </div>
    </div>
</li>