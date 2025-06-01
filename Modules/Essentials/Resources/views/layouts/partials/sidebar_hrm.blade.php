

<li class="nav-item {{  in_array( $request->segment(1), ['hrm']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#hrm-menu"
        aria-expanded="true" aria-controls="hrm-menu">
        <i class="fa fa-users"></i>
        <span>@lang('essentials::lang.hrm')</span>
    </a>
	<div id="hrm-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('essentials::lang.hrm'):</h6>
			<a class="collapse-item {{ in_array( $request->segment(1), ['hrm']) && $request->segment(2) == 'employee' ? 'active' : '' }}" href="{{action([\Modules\Essentials\Http\Controllers\DashboardController::class, 'hrmDashboard'])}}">@lang('essentials::lang.hrm')</a>
			<a class="collapse-item {{ in_array( $request->segment(1), ['hrm']) && $request->segment(2) == 'advances' ? 'active' : '' }}" href="{{action([\Modules\Essentials\Http\Controllers\AdvancesController::class, 'index'])}}">@lang('essentials::lang.hrm_advance')</a>
		
		</div>
	</div>
</li>