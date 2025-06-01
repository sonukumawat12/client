<div class="modal-dialog" role="document" style="width: 80%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DipManagementController@update',[$dip->id]), 'method' =>
        'put',
        'id' =>'edit_new_dip_form' ]) !!}
    
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_new_dip' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ref_number', __( 'petro::lang.ref_number' ) . ':*') !!}
                            {!! Form::text('ref_number',$dip->ref_number, ['class' => 'form-control ref_number', 'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.ref_number' ) ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('date_and_time', __( 'petro::lang.date_and_time' ) . ':*') !!}
                            {!! Form::text('date_and_time', @format_datetime($dip->date_and_time), ['class' => 'form-control date_and_time', 'required',
                            'placeholder' => __('petro::lang.date_and_time' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    
                    
                     <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('daily_report_date', __( 'petro::lang.daily_report_date' ) . ':*') !!}
                            {!! Form::text('daily_report_date', @format_date($dip->transaction_date), ['class' => 'form-control date_and_time', 'required', 
                            'placeholder' => __(
                            'petro::lang.daily_report_date' ) ]); !!}
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'petro::lang.location' ) . ':*') !!}
                            {!! Form::select('location_id', $business_locations, $dip->location_id , ['class' => 'form-control select2
                            fuel_tank_location', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}
                            {!! Form::select('tank_id', $tanks, $dip->tank_id, ['class' => 'form-control select2', 'placeholder'
                            => __('petro::lang.please_select'), 'id' => 'edit_dip_tank_id', 'style' => 'width:100%']); !!}
                        </div>
                    </div>

                    @if($tank_dip_chart_permission)
                    
                    <div class="form-group col-sm-2">
                        {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}
                        <div class="input-group">
                            
                            {!! Form::select('dip_reading', [], $dip->dip_reading, ['class' => 'form-control dip_reading select2', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ) ]); !!}
                            
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('\Modules\Petro\Http\Controllers\DipManagementController@addDipChart')}}?quick_add=true" data-container=".modal_dip_modal">
                                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    
                    @else
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}
                            {!! Form::text('dip_reading', $dip->dip_reading, ['class' => 'form-control dip_reading', 
                            'placeholder' => __(
                            'petro::lang.please_select' ) ]); !!}
                        </div>
                    </div>
                    @endif

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('fuel_balance_dip_reading', __( 'petro::lang.tank_fuel_balance_dip_reading' ) . ':*') !!}
                            {!! Form::text('fuel_balance_dip_reading', $dip->fuel_balance_dip_reading, ['class' => 'form-control tank_fuel_balance_dip_reading', 'id' => 'fuel_balance_dip_reading', 'readonly',
                            'placeholder' => __(
                            'petro::lang.tank_fuel_balance_dip_reading' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('current_qty', __( 'petro::lang.current_qty' ). ':') !!}
                            {!! Form::text('current_qty', $dip->current_qty, ['class' => 'form-control current_qty', 'id' =>'current_qty', 'readonly',
                            'placeholder' => __(
                            'petro::lang.current_qty' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('note', __( 'petro::lang.note' ) . ':*') !!}
                            {!! Form::textarea('note', $dip->note, ['class' => 'form-control note', 'rows' => 2,
                            'placeholder' => __(
                            'petro::lang.note' ) ]); !!}
                        </div>
                    </div>

                </div>
                
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    
    <div class="modal fade modal_dip_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <script>
        
        $('#date_and_time').datetimepicker({

          format: moment_date_format + ' ' + moment_time_format,

          ignoreReadonly: true,

      });
        
        $('#daily_report_date').datepicker("setDate", '{{@format_date($dip->transaction_date)}}');
        
        $('.select2').select2();
        $('#location_id').select2();
        
        $(document).ready(function(){
            $('#edit_dip_tank_id').trigger('change'); 
        })
        
        @if(!$tank_dip_chart_permission)
            $('#fuel_balance_dip_reading').attr("readonly", false); 
        @endif
        $('#edit_dip_tank_id').change(function(){
            let tank_id= $(this).val();

            $.ajax({
                method: 'get',
                url: "/petro/get-tank-balance-by-id/"+tank_id,
                data: {  },
                success: function(result) {
                    $('#tank_manufacturer').val(result.details.tank_manufacturer);
                    $('#tank_capacity').val(result.details.tank_capacity);
                    let html = '';
                    let dip_readings = result.dip_readings;
                    for (const [key, value] of Object.entries(dip_readings)) {
                        html += '<option value="'+value+'">'+key+'</option>';
                    }
                    $('#dip_reading').empty().append(html);
                    $('#dip_reading').trigger('change');
                },
            });
        })
        @if($tank_dip_chart_permission)
        $('#dip_reading').change(function(){
            let tank_dip_reading= $(this).val();

            $.ajax({
                method: 'get',
                url: "/superadmin/tank-dip-chart-details/get-reading-value/"+tank_dip_reading,
                data: {  },
                success: function(result) {
                    $('#fuel_balance_dip_reading').val(result.dip_reading_value);
                },
            });
        })
        @endif
        
        
    </script>