<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\TankDipChartController@update', $data->id), 'method'
        => 'put', 'id' => 'tank_dip_chart_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.edit_tank_dip_chart' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">

                <div class="row">
                    
                      <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('date_time', __( 'petro::lang.date_time' ) . ':*') !!}

                            {!! Form::text('date_time', @format_datetime($data->date), ['class' => 'form-control date_time', 'required',

                            'placeholder' => __(

                            'petro::lang.date_time' ),'readonly' ]); !!}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}
                            
                            {!! Form::text('fuel_tank_number', $data->fuel_tank_number, ['class' => 'form-control sheet_name', 'readonly',

                            'placeholder' => __('petro::lang.sheet_name' ) ]); !!}


                        </div>

                    </div>
                    
                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('sheet_name', __( 'petro::lang.sheet_name' ) . ':*') !!}

                            {!! Form::text('sheet_name', $data->sheet_name, ['class' => 'form-control sheet_name', 'required',

                            'placeholder' => __(

                            'petro::lang.sheet_name' ) ]); !!}

                        </div>

                    </div>
                    
                   
                    
                     <div class="col-md-6">

                        <div class="form-group">

                            {!! Form::label('tank_capacity', __( 'petro::lang.tank_capacity' ) . ':*') !!}

                            {!! Form::text('tank_capacity', @num_format($data->storage_volume), ['class' => 'form-control tank_capacity', 

                            'placeholder' => __(

                            'petro::lang.tank_capacity' ),'readonly' ]); !!}

                        </div>

                    </div>
                    
                     <div class="col-md-6">

                        <div class="form-group">

                            {!! Form::label('tank_manufacturer', __( 'petro::lang.tank_manufacturer' ) . ':*') !!}

                            {!! Form::text('tank_manufacturer', $data->tank_manufacturer, ['class' => 'form-control tank_manufacturer', 'required',

                            'placeholder' => __(

                            'petro::lang.tank_manufacturer' ) ]); !!}

                        </div>

                    </div>
                    
                     <div class="col-md-8">

                        <div class="form-group">

                            {!! Form::label('tank_manufacturer_contact', __( 'petro::lang.tank_manufacturer_contact' ) . '(separate by comma):*') !!}

                            {!! Form::text('tank_manufacturer_contact', $data->tank_manufacturer_phone, ['class' => 'form-control tank_manufacturer_contact', 'required',

                            'placeholder' => __('petro::lang.tank_manufacturer_contact' ) , 'style' => 'width: 100% !important']); !!}

                        </div>

                    </div>

                    <div class="clearfix"></div>

                    
                     <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('user_added', __( 'petro::lang.user_added' ) . ':*') !!}

                            {!! Form::text('user_added',$data->username, ['class' => 'form-control user_added', 'required',

                            'placeholder' => __(

                            'petro::lang.user_added' ),'readonly' ]); !!}

                        </div>

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

<script>
    $(document).ready(function() {
            $('#tank_manufacturer_contact').tagsinput({
              allowDuplicates: false
            });
            
            $('.select2').select2();
           
        });
</script>