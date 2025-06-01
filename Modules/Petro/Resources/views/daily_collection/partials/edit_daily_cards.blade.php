<div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyCardController@update',[$data->id]), 'method' =>
        'put',
        'id' =>
        'add_cards_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_collection' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                            {!! Form::text('date', @format_date($data->date), ['class' => 'form-control transaction_date', 'required',
                            'placeholder' => __(
                            'petro::lang.transaction_date' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('collection_no', __( 'petro::lang.collection_form_no' ) . ':*') !!}
                            {!! Form::text('collection_no', $data->collection_no, ['class' => 'form-control collection_form_no', 'required',
                            'placeholder' => __(
                            'petro::lang.collection_form_no' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('pump_operator', __( 'petro::lang.pump_operator' ) . ':*') !!}
                            {!! Form::select('pump_operator_id', $pump_operators, $data->pump_operator_id , ['class' => 'form-control select2
                            pump_operator', 'required', 'id' => 'pump_operator_id',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;','required']); !!}
                        </div>
                    </div>
                                
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_id', __('petro::lang.customer').':') !!}
                            {!! Form::select('customer_id', $customers, $data->customer_id, ['class' => 'form-control select2', 'style' => 'width: 100%;','required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('card_type', __('petro::lang.card_type').':') !!}
                            {!! Form::select('card_type', $card_types, $data->card_type, ['class' => 'form-control card_fields
                            select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.please_select' ) ,'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('card_number', __( 'petro::lang.card_number' ) ) !!}
                            {!! Form::text('card_number', $data->card_number, ['class' => 'form-control card_fields input_number
                            card_number',
                            'placeholder' => __(
                            'petro::lang.card_number' ),'required' ]); !!}
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('amount', __( 'petro::lang.amount' ) ) !!}
                            {!! Form::text('amount', $data->amount, ['class' => 'form-control card_fields cust_input_number
                            amount', 'required',
                            'placeholder' => __(
                            'petro::lang.amount' ) ,'required']); !!}
                        </div>
                    </div>
                    
                     <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('slip_no', __( 'petro::lang.slip_no' ) ) !!}
                            {!! Form::text('slip_no', $data->slip_no, ['class' => 'form-control card_fields 
                            slip_no', 'required',
                            'placeholder' => __(
                            'petro::lang.slip_no' ),'required' ]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="form-group">
                          {!! Form::label("card_note", __('lang_v1.payment_note') . ':') !!}
                          {!! Form::textarea("card_note", $data->card_note, ['class' => 'form-control cash_fields', 'rows' => 3]); !!}
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
        $('.select2').select2();

    </script>
<script>
    $(document).ready(function(){
        $("#customer_id").val($("#customer_id option:eq(0)").val()).trigger('change');
        
    });
</script>