<div class="modal-dialog" role="document">
    <div class="modal-content">
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
                          {!! Form::text('date', @format_date($discount->transaction_date), ['class' => 'form-control', 'required', 'readonly','placeholder' => __( 'lang_v1.date' ), 'id' => 'discount_date']); !!}
                    </div>
                </div>
                 <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_amount', __( 'ledger_discount.location' ) . ':*') !!}
                          {!! Form::text('discount_amount', $discount->location, ['class' => 'form-control input_number', 'readonly', 'placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_amount', __( 'ledger_discount.customer' ) . ':*') !!}
                          {!! Form::text('discount_amount', $discount->contact_name, ['class' => 'form-control input_number', 'readonly', 'placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                </div>
                
                <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_amount', __( 'sale.amount' ) . ':*') !!}
                          {!! Form::text('discount_amount', @num_format($discount->final_total), ['class' => 'form-control input_number', 'readonly', 'placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                    
                   
                </div>
               
                <div class="row">
    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_note', __( 'brand.note' ) . ':') !!}
                          {!! Form::textarea('discount_note', $discount->additional_notes, ['class' => 'form-control','disabled', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_note', __( 'brand.note' ) . ':') !!}
                          {!! Form::textarea('discount_note', $discount->transaction_note, ['class' => 'form-control','disabled', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
                    </div>
                    
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
           
        </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->  
