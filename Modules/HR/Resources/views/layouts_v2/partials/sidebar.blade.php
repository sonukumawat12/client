@can('hr.access')

<li class="nav-item {{ in_array($request->segment(1), ['hr']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#hr-menu"
        aria-expanded="true" aria-controls="hr-menu">
        <i class="fa fa-users"></i>
        <span>@lang('hr::lang.hr_module')</span>
    </a>
    <div id="hr-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('hr::lang.hr_module'):</h6>
            @if($hr_module)
            @can('hr.employee')
            @if($employee)
                <a class="collapse-item {{ $request->segment(1) == 'hr' && $request->segment(2) == 'employee' ? 'active' : '' }}" href="{{action('\Modules\HR\Http\Controllers\EmployeeController@index')}}">@lang('hr::lang.employee')</a>
            @endif
            @if($attendance)
                <a class="collapse-item {{ $request->segment(2) == 'attendance' ? 'active active-sub' : '' }}" href="{{action('\Modules\HR\Http\Controllers\AttendanceController@index')}}">@lang('hr::lang.attendance')</a>
            @endif
            @if($late_and_over_time)
                <a class="collapse-item {{ $request->segment(2) == 'attendance' && $request->segment(3) == 'get-late-and-overtime' ? 'active active-sub' : '' }}" href="{{action('\Modules\HR\Http\Controllers\AttendanceController@getLateOvertime')}}">@lang('hr::lang.late_and_overtime')</a>
            @endif

            @can('hr.payroll')
            @if($payroll)
                <a class="collapse-item {{ $request->segment(2) == 'payroll' ? 'active active-sub' : '' }}" href="{{action('\Modules\HR\Http\Controllers\PayrollPaymentController@index')}}">@lang('hr::lang.payroll')</a>
            @endif
            @endcan
    
            @can('hr.reports')
            @if($hr_reports)
                <a class="collapse-item {{ $request->segment(2) == 'report' ? 'active' : '' }}" href="{{action('\Modules\HR\Http\Controllers\ReportController@index')}}">@lang('hr::lang.reports')</a>
            @endif
            @endcan
    
            @can('hr.notice_board')
            @if($notice_board)
            <a class="collapse-item {{ $request->segment(2) == 'notice-board' ? 'active' : '' }}" href="{{action('\Modules\HR\Http\Controllers\NoticeBoardController@index')}}">@lang('hr::lang.notice_board')</a>
            @endif
            @endcan
            @endif
    
            <!-- Hr Settings  -->
            @can('hr.settings')
            @if($hr_settings)
                <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}" href="{{action('\Modules\HR\Http\Controllers\HrSettingsController@index')}}">@lang('hr::lang.hr_settings')</a>
            @endif
            @endcan
            @endif
        </div>
    </div>
</li>
@endcan