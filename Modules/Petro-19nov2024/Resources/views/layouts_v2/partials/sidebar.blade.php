@php

$module_array = [
    'petro_dashboard' => 0,
    'petro_daily_status' => 0,
    'tank_transfer' => 0,
    'petro_task_management' => 0,
    'pumper_management' => 0,
    'daily_collection' => 0,
    'settlement' => 0,
    'list_settlement' => 0,
    'dip_management' => 0,
    'pump_operator_dashboard' => 0
];

foreach ($module_array as $key => $module_value) {
    ${$key} = 0;
}

$business_id = request()->session()->get('user.business_id');
$subscription = Modules\Superadmin\Entities\Subscription::current_subscription($business_id);
$stock_adjustment = 0;

if (!empty($subscription)) {
    $package_details = $subscription->package_details;
    $stock_adjustment = $package_details['stock_adjustment'];

    foreach ($module_array as $key => $module_value) {
        if (array_key_exists($key, $package_details)) {
            ${$key} = $package_details[$key];
        } else {
            ${$key} = 0;
        }
    }

}


@endphp



<li class="nav-item">
    <a class="nav-link collapsed {{ in_array($request->segment(1), ['petro']) && $request->segment(2) != 'issue-customer-bill'? 'active active-sub' : '' }}" href="#" data-toggle="collapse" data-target="#petro-menu"
        aria-expanded="true" aria-controls="petro-menu">
        <i class="fa fa-tint fa-lg"></i>
        <span>@lang('petro::lang.petro')</span>
    </a>
    <div id="petro-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('petro::lang.petro'):</h6>
            @if($petro_dashboard)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'dashboard' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\PetroController@index')}}">@lang('petro::lang.dashboard')</a>
            @endif

            @if($pump_operator_dashboard)
                 <a class="collapse-item {{ $request->segment(1) == 'pump-operator' && $request->segment(2) == 'dashboard' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}">@lang('petro::lang.pump_operator_dashboard')</a>
            @endif

            @if($petro_task_management)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'tank-management' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\FuelTankController@index')}}">@lang('petro::lang.tank_management')</a>
            @endif
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-management' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\PumpController@index')}}">@lang('petro::lang.pump_management')</a>
            @if($pumper_management)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-operators' && $request->segment(3) == '' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@index')}}">@lang('petro::lang.pumper_management')</a>
            @endif
            @if($daily_collection)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'daily-collection' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\DailyCollectionController@index')}}">@lang('petro::lang.daily_collection')</a>
            @endif
            
            
            @if($settlement)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'settlement' && $request->segment(3) == 'create' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\SettlementController@create')}}">@lang('petro::lang.settlement')</a>
            @endif
            @if($list_settlement)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'settlement' && $request->segment(3) == '' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\SettlementController@index')}}">@lang('petro::lang.list_settlement')</a>
            @endif
            @if($dip_management)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'dip-management' && $request->segment(3) == '' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\DipManagementController@index')}}">@lang('petro::lang.dip_management')</a>
            @endif
            
            @if($petro_daily_status)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'daily-status_report' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@index')}}">@lang('petro::lang.daily_status_report')</a>
            @endif
            
            @if(!empty($tank_transfer) && $tank_transfer)
                <a class="collapse-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'tank-transfers' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\TankTransferController@index')}}">@lang('petro::lang.list_tank_transfer')</a>
            @endif
           
           
            @if($pump_operator_dashboard)
                 <a class="collapse-item {{ $request->segment(1) == 'pump-operator' && $request->segment(2) == 'dashboard' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@setting_dash')}}">@lang('petro::lang.pump_dashboard_settings')</a>
            @endif
            
            <a class="collapse-item {{ $request->segment(1) == 'settlement' && $request->segment(2) == 'activity-report' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\SettlementController@getUserActivityReport')}}">@lang('petro::lang.petro_activity_report')</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'settlement' && $request->segment(2) == 'day-end-settlement' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\DayEndSettlementController@index')}}">@lang('petro::lang.day_end_settlement')</a>
           
           @if($petro_sms_notifications)
               @can('petro_sms_notifications')
                <a class="collapse-item {{ $request->segment(1) == 'settlement' && $request->segment(2) == 'petro_sms_notifications' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\PetroNotificationTemplateController@index')}}">@lang('petro::lang.petro_sms_notifications')</a>
               @endcan
           @endif

        </div>
    </div>
</li>

