
    <div class="row">
        <div class="col-md-12">
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
                    'fleet::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('trip_category', __( 'fleet::lang.trip_category' )) !!}
                    {!! Form::select('trip_category', $trip_categories, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'trip_category']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'fleet::lang.customer' )) !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'contact_id']);
                    !!}
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_no', __( 'fleet::lang.vehicle_no' )) !!}
                    {!! Form::select('vehicle_no', $vehicle_numbers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'vehicle_no']);
                    !!}
                </div>
            </div>
            
            </div>
           
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools pull-right">
                
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('invoice_name', __( 'fleet::lang.invoice_name' )) !!}
                        {!! Form::select('invoice_name', $invoice_name, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'invoice_name']);
                        !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('type', __( 'fleet::lang.select_original_type' )) !!}
                        {!! Form::select('type', ['dynamic' => __( 'fleet::lang.dynamic' ), 'settings' => __( 'fleet::lang.location_settings' )], null, ['class' => 'form-control select2',
                        'required','id' => 'original_type']);
                        !!}
                    </div>
                </div>
                
                <div class="col-md-3 original_location">
                    <div class="form-group">
                        {!! Form::label('original_location', __( 'fleet::lang.orignal_location' )) !!}
                        {!! Form::select('original_location', $original_locations, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'original_location']);
                        !!}
                    </div>
                </div>
                
                <div class="col-md-3 s_original_location">
                    <div class="form-group">
                        {!! Form::label('original_location', __( 'fleet::lang.orignal_location' )) !!}
                        
                        {!! Form::select('original_location', $s_original_locations, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 's_original_location']);
                        !!}
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('logo', __( 'fleet::lang.logo' )) !!}
                        {!! Form::select('logo', $logos, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'logo']);
                        !!}
                    </div>
                </div>
                
                <div class="col-md-2">
                    <a class="btn btn-primary" id="add_create_invoice" style="margin-top: 20px; margin-bottom: 30px;"
                    href="#"><i class="fa fa-plus"></i> @lang( 'fleet::lang.save_invoice' )</a>
                </div>
                
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="route_operation_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.customer' )</th>
                                    <th>@lang( 'fleet::lang.trip_category' )</th>
                                    <th>@lang( 'fleet::lang.bowser_no' )</th>
                                    <th>@lang( 'fleet::lang.order_number' )</th>
                                    <th>@lang( 'fleet::lang.invoice_no' )</th>
                                    <th>@lang( 'fleet::lang.product' )</th>
                                    <th>@lang( 'fleet::lang.qty' )</th>
                                    <th>@lang( 'fleet::lang.road_trip_mileage' )</th>
                                    <th>@lang( 'fleet::lang.account_no' )</th>
                                    <th>@lang( 'fleet::lang.amount' )</th>
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
    <div class="modal fade payment_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
