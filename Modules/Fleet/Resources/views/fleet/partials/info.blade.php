@php logger("actual meter is $current_meter"); @endphp
<div class="row">
    <div class="col-md-12">
        <div class="col-sm-4 text-muted">
            <strong>@lang('fleet::lang.code_for_vehicle'): {{ $fleet->code_for_vehicle }}</strong><br><br>
            <strong>@lang('fleet::lang.location') : {{ $fleet->location_name }}</strong><br><br>
            <strong>@lang('fleet::lang.vehicle_number') : {{ $fleet->vehicle_number }}</strong><br><br>
            <strong>@lang('fleet::lang.vehicle_type') : {{ $fleet->vehicle_type }}</strong><br><br>
            <strong>@lang('fleet::lang.vehicle_brand') : {{ $fleet->vehicle_brand }}</strong><br><br>
            <strong>@lang('fleet::lang.vehicle_model') : {{ $fleet->vehicle_model }}</strong><br><br>
            <strong>@lang('fleet::lang.battery_detail') : {{ $fleet->battery_detail }}</strong><br><br>
            <strong>@lang('fleet::lang.tyre_detail') : {{ $fleet->tyre_detail }}</strong><br><br>
           
        </div>
        <div class="col-sm-4 text-muted">
            <strong>@lang('fleet::lang.opening_balance'): {{ @number_format($fleet->opening_balance) }}</strong><br><br>
            <strong>@lang('fleet::lang.current_balance'): {{ @number_format($fleet->opening_balance+$fleet->income) }}</strong><br><br>
            <strong>@lang('fleet::lang.starting_meter'): {{ @number_format($fleet->starting_meter) }}</strong><br><br>
            <strong>@lang('fleet::lang.actual_meter'): {{ @number_format($current_meter) }}</strong><br><br>
            
            <strong>{{__( 'fleet::lang.income_account')}}: {{ $income_acc }}</strong><br><br>
            
            <strong>@lang('fleet::lang.expense_account'): {{ $expense_acc }}</strong><br><br>
            <strong>@lang('fleet::lang.notes') : {{ $fleet->notes }}</strong><br><br>
           
        </div>
    </div>
</div>