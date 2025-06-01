<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('TransactionPaymentController@postPayContactDue'), 'method' => 'post', 'id' =>
    'pay_contact_due_form', 'files' => true ]) !!}

    {!! Form::hidden("contact_id", $contact_details->contact_id); !!}
    {!! Form::hidden("due_payment_type", $due_payment_type); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.add_payment' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        @if($due_payment_type == 'purchase')
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $contact_details->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $contact_details->supplier_business_name }}<br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('report.total_purchase'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_purchase }}</span><br>
            <strong>@lang('contact.total_paid'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_paid }}</span><br>
            <strong>@lang('contact.total_purchase_due'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_purchase - $contact_details->total_paid }}</span><br>
            @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
            <strong>@lang('lang_v1.opening_balance'): </strong>
            <span class="display_currency" data-currency_symbol="true">
              {{ $contact_details->opening_balance }}</span><br>
            <strong>@lang('lang_v1.opening_balance_due'): </strong>
            <span class="display_currency" data-currency_symbol="true">
              {{ $ob_due }}</span>
            @endif
          </div>
        </div>
        @elseif($due_payment_type == 'purchase_return')
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $contact_details->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $contact_details->supplier_business_name }}<br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('lang_v1.total_purchase_return'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_purchase_return }}</span><br>
            <strong>@lang('lang_v1.total_purchase_return_paid'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
            <strong>@lang('lang_v1.total_purchase_return_due'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_purchase_return - $contact_details->total_return_paid }}</span>
          </div>
        </div>
        @elseif(in_array($due_payment_type, ['sell']))
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('sale.customer_name'): </strong>{{ $contact_details->name }}<br>
            <br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('report.total_sell'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_invoice }}</span><br>
            <strong>@lang('contact.total_paid'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_paid }}</span><br>
            <strong>@lang('contact.total_sale_due'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_invoice - $contact_details->total_paid }}</span><br>
            @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
            <strong>@lang('lang_v1.opening_balance'): </strong>
            <span class="display_currency" data-currency_symbol="true">
              {{ $contact_details->opening_balance }}</span><br>
            <strong>@lang('lang_v1.opening_balance_due'): </strong>
            <span class="display_currency" data-currency_symbol="true">
              {{ $ob_due }}</span>
            @endif
          </div>
        </div>
        @elseif(in_array($due_payment_type, ['sell_return']))
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('sale.customer_name'): </strong>{{ $contact_details->name }}<br>
            <br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('lang_v1.total_sell_return'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_sell_return }}</span><br>
            <strong>@lang('lang_v1.total_sell_return_paid'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
            <strong>@lang('lang_v1.total_sell_return_due'): </strong><span class="display_currency"
              data-currency_symbol="true">{{ $contact_details->total_sell_return - $contact_details->total_return_paid }}</span>
          </div>
        </div>
        @endif
      </div>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("location_id" , __('purchase.business_location') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-location-arrow"></i>
              </span>
              {!! Form::select("location_id", $business_locations, $business_location_id, ['class' => 'form-control
              select2 location_id', 'required', 'style' => 'width:100%;', 'placeholder' =>
              __('lang_v1.please_select')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("payment_ref_no" , __('lang_v1.ref_no') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-link"></i>
              </span>
              {!! Form::text("payment_ref_no", $payment_ref_no, ['class' => 'form-control
               payment_ref_no', 'readonly', 'style' => 'width:100%;', 'placeholder' =>
              __('lang_v1.ref_no')]); !!}
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
              {!! Form::text("amount", $amount_formated, ['class' => 'form-control input_number',
              'data-rule-min-value' => 0,'data-rule-max-value' => $amount_formated,'data-msg-max-value' => __('contact.greater_value_not_allowed'), 'data-msg-min-value' => __('lang_v1.negative_value_not_allowed'), 'required',
              'placeholder' => 'Amount']); !!}
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
        <div class="col-md-4 text-left pd_cheque_boxes hide" >
          @if(!empty($pacakge_details['add_pd_cheque']))
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('post_dated_cheque', '1', false,
                    [ 'class' => 'input-icheck','id' => 'post_dated_cheque']); !!} {{ __( 'account.post_dated_cheque' ) }}
                </label>
            </div>
          @endif
          @if(!empty($pacakge_details['update_post_dated_cheque']))
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('update_post_dated_cheque', '1', false,
                    [ 'class' => 'input-icheck','id' => 'update_post_dated_cheque']); !!} {{ __( 'account.update_post_dated_cheque' ) }}
                </label>
            </div>
          @endif
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document'); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>

              {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , 
                                ['class' => 'form-control select2 account_id', 'id' => "account_id", 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>

        <div class="clearfix"></div>

        @include('transaction_payment.payment_type_details')
        
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary submit_btn" id="submit_btn">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('.payment_types_dropdown').trigger('change');
  $('#pay_contact_due_form').validate();
  $(".select2").select2();

  $(document).on('change', '.location_id', function(){
    let location_id = $(this).val();
    $.ajax({
      method: 'get',
      url: "/payments/get-payment-method-by-location-id/"+location_id,
      data: {  },
      contentType: 'html',
      success: function(result) {
        if(result){
          $('#method').empty().append(result);
          $('#method option:eq(0)').prop('selected', 'selected');
          $('.payment_types_dropdown').trigger('change');
        }
      },
    });
  });

  $(document).on('change', '.payment_types_dropdown', function() {
    if($('.pd_cheque_boxes')){
      if($(this).val() == "cheque"){
        $('.pd_cheque_boxes').removeClass('hide');
      } else {
        $('.pd_cheque_boxes').addClass('hide');
      }
    }
  });

  $(document).on('change', '#update_post_dated_cheque', function() {
    console.log("update_post_dated_cheque");
    
    var payment_type = $(".payment_types_dropdown").val();
    var location_id = $('#location_id').val();
    var accounting_module = $("#account_id");
    var previous_acc_id = parseInt($('.previous_account').val());
    accounting_module.attr('required', true);
    accounting_module.empty();
    if($(this).is(':checked')){
      $.ajax({
        method: 'get',
        url: '/accounting-module/get-account-group-name-dp',
        data: { group_name: "direct_bank_deposit", location_id: location_id },
        contentType: 'html',
        success: function(result) {
          accounting_module.empty().append(result);
          accounting_module.attr('required', true);
          accounting_module.val(accounting_module.find('option:first').val());
          if(previous_acc_id){
            accounting_module.val(previous_acc_id).change();
          }
        },
      });
    } else {
      $.ajax({
        method: 'get',
        url: '/accounting-module/get-account-group-name-dp',
        data: { group_name: payment_type, location_id: location_id },
        contentType: 'html',
        success: function(result) {
          accounting_module.empty().append(result);
          accounting_module.attr('required', true);
          accounting_module.val(accounting_module.find('option:first').val());
          if(previous_acc_id){
            accounting_module.val(previous_acc_id).change();
          }
        },
      });
    }
  });
  
      $(document).on('change', '#amount', function(){
            @if($due_payment_type == "sell_return" || $contact_details->type == 'supplier')
              $('button#submit_btn').prop('disabled', true);
                var amount = $(this).val();
        		paid = parseFloat();
        		var accid = $('.account_id').val(amount.replace(/,/g, ''));
        		
                $.ajax({
                    method: 'GET',
                    url: '/accounting-module/check-insufficient-balance-for-accounts',
                    success: function(result) {
                        var ids = result;
                        
                        if(ids.includes(accid)) {
                                            
                            $.ajax({
                               method: 'GET',
                            url: '/accounting-module/get-account-balance/' + accid,
                               success: function(result) {
                                
                                if(parseFloat(paid) > parseFloat(result.balance) || result.balance == null){
                                    swal({
                                        title: 'Insufficient Balance',
                                        icon: "error",
                                        buttons: true,
                                        dangerMode: true,
                                    })
                                    
                                   
                                   $('button#submit_btn').prop('disabled', true);
                                    return false;
                                  } else {
                                      $('button#submit_btn').prop('disabled', false);
                                  }
                               }
                            });
                        } else {
                          $('button#submit_btn').prop('disabled', false);
                        }
        
                    }
                });
            @endif
        });
        	
      $(document).on('change', '.account_id', function(){
          
          @if($due_payment_type == "sell_return" || $contact_details->type == 'supplier')
        	    
        	    $('button#submit_btn').prop('disabled', true);
        	    
        	    var amount = $('#amount').val();
            
                var accid = parseInt($(this).val());
                var paid = parseFloat(amount.replace(/,/g, ''));
                
                $.ajax({
                    method: 'GET',
                    url: '/accounting-module/check-insufficient-balance-for-accounts',
                    success: function(result) {
                        var ids = result;
                        if(ids.includes(accid)) {
                           
                            $.ajax({
                               method: 'GET',
                               url: '/accounting-module/get-account-balance/' + accid,
                               success: function(result) {
                                
                                if(parseFloat(paid) > parseFloat(result.balance) || result.balance == null){
                                    swal({
                                        title: 'Insufficient Balance',
                                        icon: "error",
                                        buttons: true,
                                        dangerMode: true,
                                    })
                                    
                                   $('button#submit_btn').prop('disabled', true);
                                    return false;
                                  } else {
                                      $('button#submit_btn').prop('disabled', false);
                                  }
                               }
                            });
                        } else {
                          $('button#submit_btn').prop('disabled', false);
                        }
            
                    }
                });
                
            @endif
                
      });
      
       $(document).on('click','#amount',function(){
       $("#amount").val("");
    });
  
</script>