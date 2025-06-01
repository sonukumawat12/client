
<section class="content">
    
        
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'fleet::lang.location' )) !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_number', __( 'fleet::lang.vehicle_number' )) !!}
                    {!! Form::select('vehicle_number', $vehicle_numbers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'vehicle_number']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_type', __( 'fleet::lang.vehicle_type' )) !!}
                    {!! Form::select('vehicle_type', $vehicle_types, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'vehicle_type']);
                    !!}
                </div>
            </div>
            
            </div>
            <div class="row">
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_brand', __( 'fleet::lang.vehicle_brand' )) !!}
                    {!! Form::select('vehicle_brand', $vehicle_brands, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'vehicle_brand']);
                    !!}
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_model', __( 'fleet::lang.vehicle_model' )) !!}
                    {!! Form::select('vehicle_model', $vehicle_models, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'lang_v1.all' ), 'id' => 'vehicle_model']);
                    !!}
                </div>
            </div>
          </div>
            @endcomponent
        
    

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.all_your_fleets')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_fleet_btn"
                    data-href="{{action('\Modules\Bakery\Http\Controllers\FleetController@create')}}"
                    data-container=".fleet_model">
                    <i class="fa fa-plus"></i> @lang( 'fleet::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-11">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="fleet_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.location' )</th>
                                    <th>@lang( 'fleet::lang.code_vehicle' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_number' )</th>
                                    <th>@lang( 'fleet::lang.starting_meter' )</th>
                                    <th>@lang( 'fleet::lang.ending_meter' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_type' )</th>
                                    <!--<th>@lang( 'fleet::lang.fuel_type' )</th>-->
                                    <!--<th>@lang( 'fleet::lang.income' )</th>-->
                                    <!--<th>@lang( 'fleet::lang.payment_received' )</th>-->
                                    <!--<th>@lang( 'fleet::lang.payment_due' )</th>-->
                                    <th>@lang( 'fleet::lang.opening_amount' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
