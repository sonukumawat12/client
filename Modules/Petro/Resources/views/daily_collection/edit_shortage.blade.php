<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyCollectionController@updateShortage',[$data->id]), 'method' =>
        'put',
        'id' =>
        'add_pumps_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.edit' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pump_operator', __( 'petro::lang.pump_operator' ) . ':*') !!}
                            {!! Form::select('pump_operator_id', $pump_operators, $data->pump_operator_id , ['class' => 'form-control select2
                            pump_operator', 'required', 'id' => 'pump_operator_id',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                   
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('payment_amount', __( 'petro::lang.payment_amount' ) . ':*') !!}
                            {!! Form::text('payment_amount', $data->payment_amount, ['class' => 'form-control payment_amount input_number', 'required',
                            'placeholder' => __(
                            'petro::lang.payment_amount' ) ]); !!}
                        </div>
                    </div>
                    

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.update' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('.location_id').select2();
        $('.pump_operator').select2();
    </script>