
@php
                    
    $work_shift = 0;
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp
@section('css')
    <style>





        @media (max-width: 1600px) {
            .notification-area {
                height: 45px;
                width: 100%;
            }
            .my-div1{
                height: 45px;
                width: 100%;
            }
        }


    </style>
@endsection


<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4" >
        <div class="container-fluid">
           

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="settlement_tabs" id="bs-example-navbar-collapse-1">
                <ul class="nav nav-tabs">
                    @if(!empty($pacakge_details['hrm_dashboard']))
                    <li @if(request()->segment(2) == 'dashboard') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\DashboardController::class, 'hrmDashboard'])}}">{{__('essentials::lang.hrm')}}</a></li>
                    @endif
                    
                    @if(!empty($pacakge_details['leave_type']))
                    @can('essentials.crud_leave_type')
                        <li @if(request()->segment(2) == 'leave-type') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController::class, 'index'])}}">@lang('essentials::lang.leave_type')</a></li>
                    @endcan
                    @endif
                    
                    @if(!empty($pacakge_details['leave_request']) || !empty($pacakge_details['hrm_leave']))
                        @if(auth()->user()->can('essentials.crud_all_leave') || auth()->user()->can('essentials.crud_own_leave'))
                            <li @if(request()->segment(2) == 'leave') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'index'])}}">@lang('essentials::lang.leave')</a></li>
                        @endif
                    @endif
                    
                    @if(!empty($pacakge_details['attendance']))
                        @if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
                        <li @if(request()->segment(2) == 'attendance') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'index'])}}">@lang('essentials::lang.attendance')</a></li>
                        @endif
                    @endif
                    
                    @if(!empty($pacakge_details['payroll']))
                    <li @if(request()->segment(2) == 'payroll') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'index'])}}">@lang('essentials::lang.payroll')</a></li>
                    @endif
                    
                    @if(!empty($pacakge_details['holidays']))
                    <li @if(request()->segment(2) == 'holiday') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsHolidayController::class, 'index'])}}">@lang('essentials::lang.holiday')</a></li>
                    @endif
                    
                    @can('essentials.crud_department')
                    <li @if(request()->segment(2) == 'department') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsDepartmentController::class, 'index'])}}">@lang('essentials::lang.departments')</a></li>
                    @endcan
                    
                    @can('essentials.crud_designation')
                    <li @if(request()->segment(2) == 'designation') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsDesignationController::class, 'index']) }}">@lang('essentials::lang.designations')</a></li>
                    @endcan

                    @if(auth()->user()->can('essentials.access_sales_target') && !empty($pacakge_details['hrm_sales_target']))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'sales-target') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\SalesTargetController::class, 'index'])}}">@lang('essentials::lang.sales_target')</a></li>
                    @endif

                    @if(auth()->user()->can('edit_essentials_settings') && !empty($pacakge_details['hrm_settings']))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsSettingsController::class, 'edit'])}}">@lang('business.settings')</a></li>
                    @endif
                    
                    <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'employees') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'index'])}}">@lang('essentials::lang.employees')</a></li>
                    
                    @if(!empty($pacakge_details['work_shift']))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'workshifts') class="active" @endif><a href="{{action([\Modules\Essentials\Http\Controllers\WorkShiftController::class, 'index'])}}">@lang('essentials::lang.workshifts')</a></li>
                    @endif
                    
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>
