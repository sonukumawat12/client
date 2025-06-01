<style>
    .bootstrap-tagsinput{
        width: 100% !important;
    }
</style>

<div class="modal-dialog" role="document" style="width: 60%;">

    <div class="modal-content">



        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DipManagementController@saveDipChart'),

        'method' =>

        'post',

        'class' =>

        'quick_add_dip_chart_add_form' ]) !!}
        
        <input type="hidden" value="true" name="quick_add">



        <div class="modal-header">

            <button type="button" class="close dismiss_modal_dip_modal"><span

                    aria-hidden="true">&times;</span></button>

            <h4 class="modal-title">@lang( 'petro::lang.add_dip_chart' ) </h4>

        </div>



        <div class="modal-body">
            <div class="col-md-12">

                <div class="row">
                    
                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('tank_id', __('petro::lang.tanks') . ':') !!}
                            
                            <select class="form-control select2" id="add_dip_chart_tank_id" name="tank_id">
                                <option value="">@lang('petro::lang.please_select')</option>
                                
                                @foreach($tanks as $tank)
                                    <option value="{{$tank->id}}" data-tankname="{{$tank->fuel_tank_number}}" data-manufacturer="{{$tank->tank_manufacturer}}" data-manufacturerphone="{{$tank->tank_manufacturer_phone}}" data-capacity="{{$tank->storage_volume}}">
                                        {{$tank->fuel_tank_number}}
                                    </option>
                                @endforeach
                            </select>


                        </div>

                    </div>
                    
                      <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('date_time', __( 'petro::lang.date_time' ) . ':*') !!}

                            {!! Form::text('date_time', @format_datetime(date('Y-m-d H:i')), ['class' => 'form-control date_time', 'required',

                            'placeholder' => __(

                            'petro::lang.date_time' ),'readonly' ]); !!}

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

                            'petro::lang.tank_capacity' ),'readonly' ]); !!}

                        </div>

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

                            {!! Form::text('tank_manufacturer_contact', null, ['class' => 'form-control tank_manufacturer_contact', 'required',

                            'placeholder' => __('petro::lang.tank_manufacturer_contact' ) , 'style' => 'width: 100% !important']); !!}

                        </div>

                    </div>

                    <div class="clearfix"></div>


                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}

                            {!! Form::text('dip_reading', null, ['class' => 'form-control dip_reading', 'required',

                            'placeholder' => __(

                            'petro::lang.dip_reading' ) ]); !!}

                        </div>

                    </div>





                    <div class="col-md-4">

                        <div class="form-group">

                            {!! Form::label('dip_reading_value', __( 'petro::lang.dip_reading_lts' ) . ':*') !!}

                            {!! Form::text('dip_reading_value', null, ['class' => 'form-control dip_reading_value input_number',

                            'required', 'placeholder' => __(

                            'petro::lang.dip_reading_lts' ) ]); !!}

                        </div>

                    </div>
                    
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

            <div class="clearfix"></div>

            <div class="modal-footer">

                <button type="submit" class="btn btn-primary add_dip_resetting_btn">@lang( 'messages.save' )</button>

                <button type="button" class="btn btn-default dismiss_modal_dip_modal">@lang( 'messages.close' )</button>

            </div>



            {!! Form::close() !!}

        </div><!-- /.modal-content -->

    </div><!-- /.modal-dialog -->



    <script>
        $(document).ready(function() {
            
            $(".dismiss_modal_dip_modal").on('click',function(){
                $(".modal_dip_modal").modal('hide');
            })
            
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
                    // Select the tags input element
                    var tagsInput = $('#tank_manufacturer_contact');
                    
                    // Empty the tags input
                    tagsInput.tagsinput('removeAll');
                    
                    // Add the new tag
                    tagsInput.tagsinput('add', selected.data('manufacturerphone'));

                }
                
                
            })
        });
    
        
    </script>