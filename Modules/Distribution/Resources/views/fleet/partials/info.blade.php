<div class="row">
    <div class="col-md-12">
        <div class="col-sm-4 text-muted">
            <strong>@lang('distribution::lang.code_for_vehicle'): {{ $fleet->code_for_vehicle }}</strong><br><br>
            <strong>@lang('distribution::lang.location') : {{ $fleet->location_name }}</strong><br><br>
            <strong>@lang('distribution::lang.vehicle_number') : {{ $fleet->vehicle_number }}</strong><br><br>
            <strong>@lang('distribution::lang.vehicle_type') : {{ $fleet->vehicle_type }}</strong><br><br>
            <strong>@lang('distribution::lang.vehicle_brand') : {{ $fleet->vehicle_brand }}</strong><br><br>
            <strong>@lang('distribution::lang.vehicle_model') : {{ $fleet->vehicle_model }}</strong><br><br>
            <strong>@lang('distribution::lang.battery_detail') : {{ $fleet->battery_detail }}</strong><br><br>
            <strong>@lang('distribution::lang.tyre_detail') : {{ $fleet->tyre_detail }}</strong><br><br>
            <strong>@lang('distribution::lang.notes') : {{ $fleet->notes }}</strong><br><br>
           
        </div>
    </div>
</div>