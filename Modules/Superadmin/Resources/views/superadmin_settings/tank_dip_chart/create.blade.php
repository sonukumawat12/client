<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\TankDipChartController@store'), 'method'
        => 'post', 'id' => 'tank_dip_chart_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.add_tank_dip_chart' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">

                <div class="row">
                    
                    <div class="col-md-3">

                        <div class="form-group">

                            {!! Form::label('date_time', __( 'petro::lang.date_time' ) . ':*') !!}

                            {!! Form::text('date_time', @format_datetime(date('Y-m-d H:i')), ['class' => 'form-control date_time', 'required',

                            'placeholder' => __(

                            'petro::lang.date_time' ),'readonly' ]); !!}

                        </div>

                    </div>


                    <div class="col-md-5">
                        <div class="form-group">
                            {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}
                            <div class="d-flex align-items-center">
                                {{-- <select class="form-control select2" id="add_dip_chart_tank_id" name="tank_id">
                                    <option value="">@lang('petro::lang.please_select')</option>
                                    @foreach($tanks as $tank)
                                        <option value="{{$tank->id}}" data-tankname="{{$tank->fuel_tank_number}}" data-manufacturer="{{$tank->tank_manufacturer}}" data-manufacturerphone="{{$tank->tank_manufacturer_phone}}" data-capacity="{{$tank->storage_volume}}">
                                            {{$tank->fuel_tank_number}}
                                        </option>
                                    @endforeach
                                </select> --}}
                                <input type="text" class="form-control" id="tank_names" placeholder="Added Tanks" readonly>
                                <input type="hidden" id="tank_ids" name="tank_ids">
                                <button type="button" class="btn btn-link ms-auto btn-modal add_fuel_tank" data-href="{{action('\Modules\Petro\Http\Controllers\FuelTankController@create')}}?ajax=1&multiple=1" data-container=".fuel_tank_modal">
                                    <i class="fa fa-plus"></i> @lang('messages.add') Fuel Tank
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('sheet_name', __( 'petro::lang.sheet_name' ) . ':*') !!}

                            {!! Form::text('sheet_name', null, ['class' => 'form-control sheet_name', 'required',

                            'placeholder' => __(

                            'petro::lang.sheet_name' ) ]); !!}

                        </div>

                    </div>
                    
                   
                    
                     <div class="col-md-6">

                        <div class="form-group">

                            {!! Form::label('tank_capacity', __( 'petro::lang.tank_capacity' ) . ':*') !!}

                            {!! Form::text('tank_capacity', null, ['class' => 'form-control tank_capacity', 'required',

                            'placeholder' => __(

                            'petro::lang.tank_capacity' ) ]); !!}

                        </div>
s
                    </div>
                    
                     <div class="col-md-6">

                        <div class="form-group">

                            {!! Form::label('tank_manufacturer', __( 'petro::lang.tank_manufacturer' ) . ':*') !!}

                            {!! Form::text('tank_manufacturer', null, ['class' => 'form-control tank_manufacturer', 'required',

                            'placeholder' => __(

                            'petro::lang.tank_manufacturer' ) ]); !!}

                        </div>

                    </div>
                    
                     <div class="col-md-8">

                        <div class="form-group">

                            {!! Form::label('tank_manufacturer_contact', __( 'petro::lang.tank_manufacturer_contact' ) . '(separate by comma):*') !!}

                            {!! Form::text('tank_manufacturer_contact', null, ['class' => 'form-control tank_manufacturer_contact',

                            'placeholder' => __('petro::lang.tank_manufacturer_contact' ) , 'style' => 'width: 100% !important']); !!}

                        </div>

                    </div>

                    <div class="clearfix"></div>


                    
                     <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('user_added', __( 'petro::lang.user_added' ) . ':*') !!}

                            {!! Form::text('user_added',auth()->user()->username, ['class' => 'form-control user_added', 'required',

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

<div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

<script>
    $(document).ready(function() {
            $('#tank_manufacturer_contact').tagsinput({
              allowDuplicates: false
            });
            
            $('.select2').select2();
            $("#add_dip_chart_tank_id").change(function(){
                
                $("#tank_capacity").val("");
                $("#tank_manufacturer").val("");
                $("#sheet_name").val("");
                $("#tank_manufacturer_contact").val("");
                
                if($(this).val()){
                    var selected = $("#add_dip_chart_tank_id option:selected");
                    
                    
                    $("#tank_capacity").val(__number_f(selected.data('capacity')));
                    $("#tank_manufacturer").val(selected.data('manufacturer'));
                    $("#sheet_name").val(selected.data('tankname'));
                    $('#tank_manufacturer_contact').tagsinput('add', selected.data('manufacturerphone'));
                }
                
                
            });

            $('.add_fuel_tank').click(function(){
                $('.fuel_tank_modal').modal({
                    backdrop : 'static',
                    keyboard: false
                });
            });
        });
</script>