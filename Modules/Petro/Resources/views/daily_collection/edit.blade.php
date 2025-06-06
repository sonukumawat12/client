<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyCollectionController@update',[$data->id]), 'method' =>
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
                            {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                            {!! Form::text('transaction_date', @format_date($data->created_at), ['class' => 'form-control transaction_date', 'required',
                            'placeholder' => __(
                            'petro::lang.transaction_date' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('collection_form_no', __( 'petro::lang.collection_form_no' ) . ':*') !!}
                            {!! Form::text('collection_form_no', $data->collection_form_no, ['class' => 'form-control collection_form_no', 'required',
                            'placeholder' => __(
                            'petro::lang.collection_form_no' ), 'readonly' ]); !!}
                        </div>
                    </div>

                    

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
                            {!! Form::label('daily_shift_number', "Daily Shift:") !!}
                            {!! Form::select('daily_shift_number', $assigned_operators, $data->daily_shift, [
                                'class' => 'form-control select2',
                                'required' => false,
                                'placeholder' => __('petro::lang.please_select')
                            ]) !!}
                        </div>
                    </div>
                    @if(isset($data->shift_no))
                    <div class="col-md-6" id="shift_number_wrapper">
                        <div class="form-group">
                            {!! Form::label('shift_number', __( 'petro::lang.shift_number' ) . ':') !!}
                            {!! Form::select('shift_number', $shiftNumbers, $data->shift_no, [
                                'class' => 'form-control select2',
                                'required' => false,
                                'placeholder' => __('petro::lang.please_select')
                            ]) !!}
                        </div>
                    </div>
                    @else
                    <div class="col-md-6" id="shift_number_wrapper" style="display:none;">
                        <div class="form-group">
                            {!! Form::label('shift_number', __( 'petro::lang.shift_number' ) . ':') !!}
                            {!! Form::select('shift_number', $shiftNumbers, $data->shift_no, [
                                'class' => 'form-control select2',
                                'required' => false,
                                'placeholder' => __('petro::lang.please_select')
                            ]) !!}
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('balance_collection', __( 'petro::lang.balance_collection' ) . ':*') !!}
                            {!! Form::text('balance_collection', $data->balance_collection, ['class' => 'form-control balance_collection input_number', 'required',
                            'placeholder' => __(
                            'petro::lang.balance_collection' ),  'readonly' => 'readonly' ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('current_amount', __( 'petro::lang.current_amount' ) . ':*') !!}
                            {!! Form::text('current_amount', $data->current_amount, ['class' => 'form-control current_amount input_number', 'required',
                            'placeholder' => __(
                            'petro::lang.current_amount' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'petro::lang.location' ) . ':*') !!}
                            {!! Form::select('location_id', $locations, $data->location_id , ['class' => 'form-control select2
                            location_id', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
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
        $('.pump_operator').trigger('change');

        $('#pump_operator_id').change(function(){
            pump_operator_id = $('#pump_operator_id').val();

            $.ajax({
                method: 'get',
                url: '/petro/daily-collection/get-balance-collection/'+pump_operator_id,
                data: {  },
                success: function(result) {
                    if(result){
                        $('#balance_collection').val(result.balance_collection);
                    }
                },
            });
        });
    </script>

    <script>
    $(document).ready(function () {
    $('#pump_operator_id').change(function () {
        let selected = $(this).val();

        if (selected) {
            $('#shift_number_wrapper').show();
        } else {
            $('#shift_number_wrapper').hide();
            $('#shift_number').val(''); // reset selection
        }
    });
});

</script>