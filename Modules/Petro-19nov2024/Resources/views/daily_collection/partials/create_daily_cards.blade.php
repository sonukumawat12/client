<div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyCardController@store'), 'method' =>
        'post',
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
                            {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control transaction_date', 'id' => 'add_date', 'required',
                            'placeholder' => __(
                            'petro::lang.transaction_date' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('collection_no', __( 'petro::lang.collection_form_no' ) . ':*') !!}
                            {!! Form::text('collection_no', $collection_form_no, ['class' => 'form-control collection_form_no', 'id' => 'add_collection_no', 'required',
                            'placeholder' => __(
                            'petro::lang.collection_form_no' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('pump_operator', __( 'petro::lang.pump_operator' ) . ':*') !!}
                            <div id="add_pump_operator_id_div">
                                {!! Form::select('pump_operator_id', $pump_operators, null , ['class' => 'form-control select2
                                    pump_operator', 'required', 'id' => 'add_pump_operator_id','placeholder' => __(
                                    'petro::lang.please_select' ), 'style' => 'width: 100%;','required']); !!}
                            </div>
                            
                            <input type="text" class="form-control hide" id="add_pump_operator_id_text" readonly>
                        </div>
                    </div>
                                
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_id', __('petro::lang.customer').':') !!}
                            {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'id' => 'add_customer_id', 'style' => 'width: 100%;','required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('card_type', __('petro::lang.card_type').':') !!}
                            {!! Form::select('card_type', $card_types, null, ['class' => 'form-control card_fields
                            select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.please_select' ) , 'id' => 'add_card_type']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('card_number', __( 'petro::lang.card_number' ) ) !!}
                            {!! Form::text('card_number', null, ['class' => 'form-control card_fields input_number
                            card_number',
                            'placeholder' => __(
                            'petro::lang.card_number' ), 'id' => 'add_card_number' ]); !!}
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('amount', __( 'petro::lang.amount' ) ) !!}
                            {!! Form::text('amount', null, ['class' => 'form-control card_fields cust_input_number
                            amount', 
                            'placeholder' => __(
                            'petro::lang.amount' ) , 'id' => 'add_amount']); !!}
                        </div>
                    </div>
                    
                     <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('slip_no', __( 'petro::lang.slip_no' ) ) !!}
                            {!! Form::text('slip_no', null, ['class' => 'form-control card_fields 
                            slip_no', 
                            'placeholder' => __(
                            'petro::lang.slip_no' ), 'id' => 'add_slip_no' ]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="form-group">
                          {!! Form::label("card_note", __('lang_v1.payment_note') . ':') !!}
                          {!! Form::textarea("card_note", null, ['class' => 'form-control cash_fields','id' => 'add_card_note', 'rows' => 3]); !!}
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary" id="add_row">@lang( 'lang_v1.add' )</button>
                   
                </div>
                
                <div class="row">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>{{__('petro::lang.customer')}}</th>
                                <th>{{__('petro::lang.card_type')}}</th>
                                <th>{{__( 'petro::lang.card_number' )}}</th>
                                <th>{{__( 'petro::lang.amount' )}}</th>
                                <th>{{__( 'petro::lang.slip_no' )}}</th>
                                <th>*</th>
                            </tr>
                        </thead>
                        <tbody id="added_details"></tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary hide add_card_collection_btn">@lang( 'messages.save' )</button>
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