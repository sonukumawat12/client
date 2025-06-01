<li class="nav-item @if( in_array($request->segment(1), ['family-members', 'superadmin', 'pay-online'])) {{'active active-sub'}} @endif">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#repair-menu"
        aria-expanded="true" aria-controls="repair-menu">
       <i class="fa fa-cog"></i>
        <span>{{__('repair::lang.repair')}}</span>
    </a>
    <div id="repair-menu" class="collapse" aria-labelledby="repair-menu"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">{{__('repair::lang.repair')}}:</h6>
            
            <a class="collapse-item" href="{{action('\Modules\Repair\Http\Controllers\DashboardController@index')}}">{{__('repair::lang.repair')}}</a>
            @if(auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all'))
            
                <a class="collapse-item {{ request()->segment(2) == 'job-sheet' && empty(request()->segment(3)) ? 'active' : '' }}" href="{{action('\Modules\Repair\Http\Controllers\JobSheetController@index')}}">@lang('repair::lang.job_sheets')</a>
            @endif

            @can('job_sheet.create')
            
                <a class="collapse-item {{ request()->segment(2) == 'job-sheet' && request()->segment(3) == 'create' ? 'active' : '' }}" href="{{action('\Modules\Repair\Http\Controllers\JobSheetController@create')}}">@lang('repair::lang.add_job_sheet')</a>
            @endcan

            @if(auth()->user()->can('repair.view') || auth()->user()->can('repair.view_own'))
                 <a class="collapse-item {{ request()->segment(2) == 'repair' && empty(request()->segment(3)) ? 'active active-sub' : '' }}" href="{{action('\Modules\Repair\Http\Controllers\RepairController@index')}}">@lang('repair::lang.list_invoices')</a>
            @endif
            @can('repair.create')
            
                <a class="collapse-item {{ request()->segment(2) == 'repair' && request()->segment(3) == 'create' ? 'active active-sub' : '' }}" href="{{ action('SellPosController@create'). '?sub_type=repair'}}">@lang('repair::lang.add_invoice')</a>
            
            @endcan
            @if(auth()->user()->can('brand.view') || auth()->user()->can('brand.create'))
            
                <a class="collapse-item {{ request()->segment(1) == 'brands' ? 'active active-sub' : '' }}" href="{{action('\Modules\Repair\Http\Controllers\RepairBrandController@index')}}">@lang('brand.brands')</a>
            @endif
            @if (auth()->user()->can('edit_repair_settings'))
                
                <a class="collapse-item {{ request()->segment(1) == 'repair' && request()->segment(2) == 'repair-settings' ? 'active active-sub' : '' }}" href="{{action('\Modules\Repair\Http\Controllers\RepairSettingsController@index')}}">@lang('messages.settings')</a>
                
            @endif
                    
        </div>
    </div>
</li>