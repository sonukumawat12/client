<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('CustomerStatementController@postPayTotalStatement',[$statement->id]), 'method' => 'post', 'id' =>
    'transaction_payment_add_form', 'files' => true ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'contact.pay_total_statement' )</h4>
    </div>
    
    <input type="hidden" id="location_id" value="{{$business_location_id}}">

    <div class="modal-body">
    @if(!empty($payment))
        <div class="alert alert-success">
            <div class="row">
                <div class="col-sm-12">
                    <b>@lang('contact.already_paid')</b>
                </div>
                <div class="col-sm-6">
                    <b>@lang('lang_v1.transaction_date'): </b>{{@format_datetime($payment->paid_on)}} <br>
                    <b>@lang('contact.system_entered_date'): </b>{{@format_datetime($payment->created_at)}} <br>
                    <b>@lang('lang_v1.payment_ref_no'): </b>{{$payment->payment_ref_no}} <br>
                </div>
                
                <div class="col-sm-6">
                    
                  @php
                        $payment_method_html = "";
                        if (in_array(strtolower($payment->method), ['bank_transfer', 'direct_bank_deposit', 'bank', 'cheque'])) {
                            $acc_id = $payment->account_id;
                            $bank_account = \App\Account::find($acc_id);
                            if (!empty($bank_account)) {
                                $payment_method_html .= '<br><b>Bank Name:</b> ' . $bank_account->name . '</br>';
                            }
                    
                            if (!empty($payment->cheque_number)) {
                                $payment_method_html .= '<b>Cheque Number:</b> ' . $payment->cheque_number . '</br>';
                            }
                        }
                    @endphp
                    
                    <b>@lang('lang_v1.created_by'):</b> {{ $payment->username }} <br>
                    <b>@lang('lang_v1.payment_method'):</b> {{ ucfirst(str_replace('_', ' ', $payment->method)) }} <br>
                    
                    {!! $payment_method_html !!}
                    
                    @if (!empty($payment->cheque_date) && in_array(strtolower($payment->method), ['bank_transfer', 'direct_bank_deposit', 'bank']))
                        <b>Cheque Date:</b> {{ @format_date($payment->cheque_date) }} </br>
                    @endif
     
                </div>
                
            </div>
        </div>
    @else
        @if(empty($paid_transactions))
          <div class="row">
            
            <div class="col-md-6">
              <div class="well">
                <strong>@lang('sale.total_amount'): </strong><span class="display_currency"
                  data-currency_symbol="true">{{ $statement_due_amount }}</span><br>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="well">
                <strong>@lang('lang_v1.contact'): </strong><span class="display_currency"
                  data-currency_symbol="true">{{ $contact->name }}</span><br>
              </div>
            </div>
          </div>
          <div class="row payment_row">
            <div class="col-md-4" style="display:none;">
              <div class="form-group">
                <label>Ref No :</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  <input class="form-control input_number" required="" placeholder="Ref No" name="refNo" type="text" value="{{$payment_ref_no}}" id="refNo" aria-required="true" readonly>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label("amount" , __('sale.amount') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  {!! Form::text("amount", @num_format($statement_due_amount), ['class' => 'form-control input_number',
                  'required', 'placeholder' => 'Amount','readonly' => 'readonly']); !!}
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control',
                  'readonly', 'required']); !!}
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2
                  payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
                </div>
              </div>
            </div>
            <div class="col-md-4 account_module">
              <div class="form-group">
                {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id :
                  '' , ['class' => 'form-control account_id select2', 'id' => "account_id", 'style' => 'width:100%;','required']); !!}
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                {!! Form::file('document'); !!}
              </div>
            </div>
            <div class="clearfix"></div>
            @include('transaction_payment.payment_type_details')
            
            @php
                        
                $business_id = request()
                    ->session()
                    ->get('user.business_id');
                
                $pacakge_details = [];
                    
                $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
                if (!empty($subscription)) {
                    $pacakge_details = $subscription->package_details;
                }
            
            @endphp
            
            @if(!empty($pacakge_details['show_post_dated_cheque']))
              <div class="col-md-6 text-left" >
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('post_dated_cheque', '1', false,
                            [ 'class' => 'input-icheck','id' => 'post_dated_cheque']); !!} {{ __( 'account.post_dated_cheque' ) }}
                        </label>
                    </div>
                </div>
                @if(!empty($pacakge_details['update_post_dated_cheque']))
                <div class="col-md-6 text-left" >
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('update_post_dated_cheque', '1', false,
                            [ 'class' => 'input-icheck','id' => 'update_post_dated_cheque']); !!} {{ __( 'account.update_post_dated_cheque' ) }}
                        </label>
                    </div>
                </div>
                @endif
                
            @endif
            
            <div class="col-md-12">
              <div class="form-group">
                {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
                {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
              </div>
            </div>
          </div>
        @else
            <div class="alert alert-danger">@lang('contact.bills') <b>{{implode(', ',$paid_transactions) }}</b> @lang('contact.are_already_paid')</div>
        @endif
    @endif
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary submit_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('.payment_types_dropdown').trigger('change');
  
  $('#paid_on').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format,
      ignoreReadonly: true,
  });

  $('#amount').change(function(){
		paid = parseFloat($('#amount').val());

		var account_balance = parseFloat($('#account_id option:selected').data('account_balance'));
		if($('#account_id option:selected').data('check_insufficient_balance')){
			if(paid > account_balance){
              $('.submit_btn').prop('disabled', true);
				Insufficient_balance_swal();
			}else{
              $('.submit_btn').prop('disabled', false);
            }
		}
	});

</script>