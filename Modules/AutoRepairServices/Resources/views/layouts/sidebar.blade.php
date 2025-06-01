<li class="nav-item @if( in_array($request->segment(1), ['family-members', 'superadmin', 'pay-online'])) {{'active active-sub'}} @endif">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#autorepair-menu"
        aria-expanded="true" aria-controls="autorepair-menu">
       <i class="fa fa-cog"></i>
        <span>{{__('autorepairservices::lang.auto_repair_services')}}</span>
    </a>
    <div id="autorepair-menu" class="collapse" aria-labelledby="autorepair-menu"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">{{__('autorepairservices::lang.auto_repair_services')}}:</h6>
            
            <a class="collapse-item" href="{{action('\Modules\AutoRepairServices\Http\Controllers\DashboardController@index')}}">{{__('autorepairservices::lang.auto_repair_services')}}</a>
            @if(auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all'))
            
                <a class="collapse-item {{ request()->segment(2) == 'job-sheet' && empty(request()->segment(3)) ? 'active' : '' }}" href="{{action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@index')}}">@lang('autorepairservices::lang.job_sheets')</a>
            @endif

            @can('job_sheet.create')
            
                <a class="collapse-item {{ request()->segment(2) == 'job-sheet' && request()->segment(3) == 'create' ? 'active' : '' }}" href="{{action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@create')}}">@lang('autorepairservices::lang.add_job_sheet')</a>
            @endcan

            @if(auth()->user()->can('repair.view') || auth()->user()->can('repair.view_own'))
                 <a class="collapse-item {{ request()->segment(2) == 'repair' && empty(request()->segment(3)) ? 'active active-sub' : '' }}" href="{{action('\Modules\AutoRepairServices\Http\Controllers\RepairController@index')}}">@lang('autorepairservices::lang.list_invoices')</a>
            @endif
            @can('repair.create')
            
                <a class="collapse-item {{ request()->segment(2) == 'repair' && request()->segment(3) == 'create' ? 'active active-sub' : '' }}" href="{{ action('SellPosController@create'). '?sub_type=repair'}}">@lang('autorepairservices::lang.add_invoice')</a>
            
            @endcan
            @if(auth()->user()->can('brand.view') || auth()->user()->can('brand.create'))
            
                <a class="collapse-item {{ request()->segment(1) == 'brands' ? 'active active-sub' : '' }}" href="{{action('\Modules\AutoRepairServices\Http\Controllers\AutorepairBrandController@index')}}">@lang('brand.brands')</a>
            @endif
            @if (auth()->user()->can('edit_repair_settings'))
                
                <a class="collapse-item {{ request()->segment(1) == 'repair' && request()->segment(2) == 'repair-summary' ? 'active active-sub' : '' }}" href="{{action('\Modules\AutoRepairServices\Http\Controllers\ServiceReportController@index')}}">Service Reports</a>
                
            @endif
                    
            @if (auth()->user()->can('edit_repair_settings'))
                
                <a class="collapse-item {{ request()->segment(1) == 'repair' && request()->segment(2) == 'repair-settings' ? 'active active-sub' : '' }}" href="{{action('\Modules\AutoRepairServices\Http\Controllers\RepairSettingsController@index')}}">@lang('messages.settings')</a>
                
            @endif
                    
        </div>
    </div>
</li>