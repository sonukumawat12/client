<style>
    .bootstrap-tagsinput{
        width: 100% !important;
    }
</style>

<div class="modal-dialog" role="document">

    <div class="modal-content">



        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DipManagementController@updateDipChart',[$data->id]),

        'method' =>

        'post',

        'id' =>

        'dip_chart_edit_form' ]) !!}



        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

                    aria-hidden="true">&times;</span></button>

            <h4 class="modal-title">@lang( 'petro::lang.edit_dip_chart' ) </h4>

        </div>



        <div class="modal-body">
            <div class="col-md-12">

                <div class="row">
                    
                    <div class="col-md-6">

                        <div class="form-group">

                            {!! Form::label('dip_reading', __( 'petro::lang.dip_reading' ) . ':*') !!}

                            {!! Form::text('dip_reading', $data->dip_reading, ['class' => 'form-control dip_reading', 'required',

                            'placeholder' => __(

                            'petro::lang.dip_reading' ) ]); !!}

                        </div>

                    </div>





                    <div class="col-md-6">

                        <div class="form-group">

                            {!! Form::label('dip_reading_value', __( 'petro::lang.dip_reading_lts' ) . ':*') !!}

                            {!! Form::text('dip_reading_value', $data->dip_reading_value, ['class' => 'form-control dip_reading_value input_number',

                            'required', 'placeholder' => __(

                            'petro::lang.dip_reading_lts' ) ]); !!}

                        </div>

                    </div>
                    
                </div>

            </div>

            <div class="clearfix"></div>

            <div class="modal-footer">

                <button type="submit" class="btn btn-primary add_dip_resetting_btn">@lang( 'messages.save' )</button>

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