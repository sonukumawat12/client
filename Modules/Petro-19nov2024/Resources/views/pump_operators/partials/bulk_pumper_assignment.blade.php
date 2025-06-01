
<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">

        {!! Form::open(['url' =>
        action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@storeBulk'), 'method' =>
        'post',
        'id' =>
        'receive_pump_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <div style="display: flex">
                <h4 class="modal-title" style="width: 80%">@lang( 'petro::lang.assign_pumps' )</h4>
                {!! Form::label('shift_number', __( 'petro::lang.shift_number' ) . ' : ' . (sprintf("%04d", $shift_number + 1)), ['style' => 'font-size: 23px; color: red; font-weight: bold;']) !!}
            </div>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('date', __( 'petro::lang.date' ) . ':*') !!}
                                {!! Form::input('datetime-local', 'date', 
                                    \Carbon\Carbon::now()->format('Y-m-d\TH:i'), 
                                    ['class' => 'form-control', 'required', 'placeholder' => __('petro::lang.please_select'), 'style' => 'width: 100%;']) !!}

                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pump_operator', __( 'petro::lang.pump_operator' ) . ':*') !!}
                                {!! Form::select('pump_operator', $pump_operators,
                                null , ['class' => 'form-control select2','required',
                                'placeholder' => __(
                                'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pump', __( 'petro::lang.pump' ) . ':*') !!}
                                {!! Form::select('pump[]', $pumps,
                                null , ['class' => 'form-control select2','multiple', 'style' => 'width: 100%;','required']); !!}
                            </div>
                        </div>
                    
                    </div>

                   
                </div>
            </div>
            <br>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary confirm_meter_reading_btn">@lang('messages.submit' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

   
    <script>
        $(".select2").select2();
    </script>
    