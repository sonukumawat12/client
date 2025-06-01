<div class="pos-tab-content">

    <div class="row">
        {!! Form::open(['url' => route('vehicle.updateVehicle',7) , 'id' => 'add_petro_fuel_quota_form']) !!}
        {!! Form::hidden('action',  'add_petro_fuel_quota'); !!}

        <div class="col-xs-4">
            <div class="form-group">

                {!! Form::label('date', __('vehicle.date_time') . ':') !!}
                {!! Form::text('date',  null, ['class' => 'form-control date_picker', 'placeholder' => 'Please select', 'id' => 'petro_quota_date','required']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('vehicle_category_id', __('vehicle.vehicle_category') . ':') !!}
                {!! Form::select('vehicle_category_id',count($vehicleCategories)?$vehicleCategories:[], null, ['class' => 'form-control', 'placeholder' => 'Please select', 'id' => 'vehicle_category']); !!}
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('vehicle_classification_id', __('vehicle.vehicle_classification') . ':') !!}
                <div class="input-group">
                    {!! Form::select('vehicle_classification_id', !count($vehicleClassification)?$vehicleClassification:[],null , ['class' =>
                    'form-control', 'id' => 'vehicle_classification_id', 'placeholder' => 'General']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('fuel_litters_allowed', __('vehicle.fuel_litters_allowed') . ':') !!}
                {!! Form::number('fuel_litters_allowed',  null, ['id' => 'fuel_litters_allowed', 'class' => 'form-control', 'placeholder' => 'Please select']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('re_fill_cycle_in_hrs', __('vehicle.re_fill_cycle_in_hrs') . ':') !!}
                {!! Form::select('re_fill_cycle_in_hrs',count($fuelReFillingCycle)?$fuelReFillingCycle:[], 0, ['id' => 're_fill_cycle_in_hrs', 'class' => 'form-control', 'placeholder' => 'Please select']); !!}
            </div>
        </div>

        <div class="col-xs-2">
            <button class="btn btn-primary" type="submit" style="margin-top: 22px;" id="petro_fuel_quota_add">@lang('messages.add')</button>
        </div>
        {!! Form::close() !!}
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'vehicle.petro_fuel_quota' )])

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="vehicle_fuel_quota_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang( 'vehicle.date' )</th>
                    <th>@lang( 'vehicle.vehicle_category' )</th>
                    <th>@lang( 'vehicle.vehicle_classification' )</th>
                    <th>@lang( 'vehicle.fuel_litters_allowed' )</th>
                    <th>@lang( 'vehicle.re_fill_cycle_in_hrs' )</th>  
                    <th>@lang( 'lang_v1.action' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</div>
