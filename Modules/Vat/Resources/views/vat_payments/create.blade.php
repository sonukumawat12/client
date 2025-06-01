
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatPaymentController@store'), 'method' =>
                    'post', 'id' => 'issue_bill_customer_form' ])
                    !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'vat::lang.add' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
               
                <div class="col-md-12">
                    
                    <div class="row">
                   
                      
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('form_no', __( 'vat::lang.form_no' )) !!}
                              {!! Form::text('form_no', $form_no, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
                              __( 'vat::lang.form_no')]);
                              !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('date', __( 'vat::lang.date' )) !!}
                              {!! Form::text('date', null, ['class' => 'form-control', 'required','readonly', 'placeholder' =>
                              __( 'vat::lang.date')]);
                              !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('contact_id', __( 'vat::lang.customer' )) !!}
                              {!! Form::select('contact_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
                              __( 'vat::lang.please_select')]);
                              !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('amount', __( 'vat::lang.amount' )) !!}
                              {!! Form::text('amount', null, ['class' => 'form-control', 'required', 'placeholder' =>
                              __( 'vat::lang.amount')]);
                              !!}
                            </div>
                        </div>
                        
                        
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('payable_account_id', __( 'vat::lang.vat_payable_account' )) !!}
                              {!! Form::select('payable_account_id', $payable_accounts, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
                              __( 'vat::lang.please_select')]);
                              !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('payment_method', __( 'vat::lang.payment_method' )) !!}
                              {!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control payment_method select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
                              __( 'vat::lang.please_select')]);
                              !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        
                         <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('payment_account_id', __( 'vat::lang.accounting_module' )) !!}
                              {!! Form::select('payment_account_id', [], null, ['class' => 'form-control payment_account_id select2', 'style' => 'width:100%;', 'required', 'placeholder' =>
                              __( 'vat::lang.please_select')]);
                              !!}
                            </div>
                        </div>
                        
                        <div class="bank_fields">
                             <div class="col-md-4">
                                <div class="form-group">
                                  {!! Form::label('cheque_date', __( 'vat::lang.cheque_date' )) !!}
                                  {!! Form::date('cheque_date', null, ['class' => 'form-control bank_field',  'placeholder' =>
                                  __( 'vat::lang.cheque_date')]);
                                  !!}
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                  {!! Form::label('cheque_number', __( 'vat::lang.cheque_number' )) !!}
                                  {!! Form::text('cheque_number', null, ['class' => 'form-control bank_field',  'placeholder' =>
                                  __( 'vat::lang.cheque_number')]);
                                  !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="bank_transfer">
                
                            <div class="col-md-4">
                                <div class="form-group">
                                  {!! Form::label('to_account_no', __( 'vat::lang.to_account_no' )) !!}
                                  {!! Form::text('to_account_no', null, ['class' => 'form-control transfer_field',  'placeholder' =>
                                  __( 'vat::lang.to_account_no')]);
                                  !!}
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                  {!! Form::label('recipient_name', __( 'vat::lang.recipient_name' )) !!}
                                  {!! Form::text('recipient_name', null, ['class' => 'form-control transfer_field',  'placeholder' =>
                                  __( 'vat::lang.recipient_name')]);
                                  !!}
                                </div>
                            </div>
                        
                        </div>
                           
                        
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                
                            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
                
                            {!! Form::textarea("note", null, ['class' => 'form-control', 'rows' => 3]); !!}
                
                          </div>
                
                        </div>
                    </div>
                    
                </div>
                 
                
            </div>
           
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

<script>
  $('#date').datepicker("setDate" , new Date());
  $('.select2').select2();
  $(".bank_fields").hide();
  $(".bank_transfer").hide();
</script>
