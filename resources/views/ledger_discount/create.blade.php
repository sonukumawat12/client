<div class="modal fade" id="add_discount_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog"  style="width: 60%"  role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action('LedgerDiscountController@store'), 'method' => 'post', 'id' => 'add_discount_form' ]) !!}
            <input type="hidden" name="contact_id" value="{{$contact->id}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.add_discount')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_no', __( 'sale.discount_no' ) . ':*') !!}
                          {!! Form::text('discount_no', $discount_no, ['class' => 'form-control input_number', 'required','readonly', 'placeholder' => __( 'sale.discount_no' ) ]); !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_date', __( 'lang_v1.date' ) . ':*') !!}
                          {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.date' ), 'id' => 'discount_date']); !!}
                    </div>
                </div>
                <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('location_id',  __('sale.location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['id'=>'discount_location_id','required','class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('discount_amount', __( 'sale.amount' ) . ':*') !!}
                          {!! Form::text('discount_amount', null, ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                </div>
                <div class="row">
                    
                    <div class="form-group col-md-6">
                        {!! Form::label('against_invoices',  __('sale.against_invoices') . ':') !!}
                        {!! Form::select('against_invoices', ['no' => __('lang_v1.no'),'yes' => __('lang_v1.yes')], null, ['id'=>'against_invoices','class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                    
                    <div class="form-group col-md-6" id="discount_invoice_div">
                        {!! Form::label('discount_invoice',  __('sale.invoice') . ':') !!}
                        {!! Form::select('discount_invoice', $due_invoices, null, ['id'=>'discount_invoice','class' => 'form-control select2', 'placeholder' => __('lang_v1.please_select'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                <div class="row" id="discount_invoices" style="margin-bottom: 15px; margin-top: 15px;">
                    <div class="col-md-12">
                        <table width="100%" id="discount_invoices_tbl">
                            <thead>
                                <tr>
                                    <th>{{__('sale.invoice_no')}}</th>
                                    <th>{{__('sale.invoice_amount')}}</th>
                                    <th>{{__('sale.discount_amount')}}</th>
                                    <th>*</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                                
                        </table>
                    </div>
                </div>
                <div class="row">
    
                    <div class="form-group col-md-12">
                        {!! Form::label('discount_note', __( 'brand.note' ) . ':') !!}
                          {!! Form::textarea('discount_note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
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
</div>