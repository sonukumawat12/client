<div class="modal-dialog" role="document">
    <div class="modal-content">
            {!! Form::open(['url' => action('LedgerDiscountController@update',[$discount->id]), 'method' => 'put', 'id' => 'add_discount_form' ]) !!}
            <input type="hidden" name="contact_id" value="{{$contact->id}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.add_discount')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_no', __( 'sale.discount_no' ) . ':*') !!}
                          {!! Form::text('discount_no', $discount->invoice_no, ['class' => 'form-control input_number', 'required','readonly', 'placeholder' => __( 'sale.discount_no' ) ]); !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_date', __( 'lang_v1.date' ) . ':*') !!}
                          {!! Form::text('date', null, ['class' => 'form-control', 'required', 'readonly','placeholder' => __( 'lang_v1.date' ), 'id' => 'discount_date']); !!}
                    </div>
                </div>
                <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('location_id',  __('sale.location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, $discount->location_id, ['id'=>'discount_location_id','required','class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_amount', __( 'sale.amount' ) . ':*') !!}
                          {!! Form::text('discount_amount', @num_format($discount->final_total), ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                </div>
               
                <div class="row">
    
                    <div class="form-group col-md-12">
                        {!! Form::label('discount_note', __( 'brand.note' ) . ':') !!}
                          {!! Form::textarea('discount_note', $discount->additional_notes, ['class' => 'form-control', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->  
<script>
    $(".select2").select2();
    $('#discount_date').datepicker("setDate" , '{{$discount->transaction_date}}');
    
</script>