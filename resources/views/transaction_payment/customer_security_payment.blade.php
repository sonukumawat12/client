<div class="modal-dialog" role="document">

  <div class="modal-content">



    {!! Form::open(['url' => action('TransactionPaymentController@postSecurityDeposit', $contact_id), 'method' =>

    'post', 'id' => 'add_security_deposit_form', 'files' => true ]) !!}



    {!! Form::hidden("contact_id", $contact_id, ['id' => 'contact_id']); !!}

    <div class="modal-header">

      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

          aria-hidden="true">&times;</span></button>

      <h4 class="modal-title">@if($contact_details == "customer") @lang( 'lang_v1.security_deposit' ) @else @lang('contact.supplier_security_deposit') @endif</h4>

    </div>

    <input type="hidden" name="type" value="security_deposit">

    <div class="modal-body">

      <div class="row">

        <div class="col-md-6">

          <div class="well">

            <strong>@if($contact_details == "customer") @lang('lang_v1.customer') @else @lang('lang_v1.supplier') @endif: </strong>{{ $contact_details->name }}<br>

          </div>

        </div>

        <div class="col-md-6">

          @if(!empty($security_deposit_already))

          <button type="button" class="btn btn-flat btn-danger pull-right" id="refund_btn">@lang('lang_v1.refund')</button>

          @endif 

        </div>

      </div>

      <input type="hidden" name="refund_transaction_id" id="refund_transaction_id" value="@if(!empty($security_deposit_already)){{$security_deposit_already->id}}@endif">

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

              {!! Form::text("amount", null, ['class' => 'form-control input_number', 'required', 'placeholder' =>

              'Amount']); !!}

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

              {!! Form::text("paid_on", @format_datetime(now()), ['class' => 'form-control', 'readonly', 'required']); !!}

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

              {!! Form::select("method", $payment_types, null, ['class' => 'form-control select2

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
        
        <div class="col-md-4 text-left pd_cheque_boxes hide">
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

        <div class="col-md-6 account_id_div">

          <div class="form-group">

            {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}

            <div class="input-group">

              <span class="input-group-addon">

                <i class="fa fa-money"></i>

              </span>

              {!! Form::select("account_id", [],null, ['class' => 'form-control

              select2 account_id', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' => 'width:100%;']);

              !!}

            </div>

          </div>

        </div>

        <div class="col-md-6 account_id_div">

          <div class="form-group">

            {!! Form::label("current_liability_account" , ($contact_details->type == "customer" ? __('lang_v1.current_liability_account') : __('contact.current_asset_account')) . ':') !!}

            <div class="input-group">

              <span class="input-group-addon">

                <i class="fa fa-money"></i>

              </span>

              {!! Form::select("current_liability_account", $accounts, $customer_deposit_account_id  ,

              ['class' => 'form-control

              select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "current_liability_account", 'style' =>

              'width:100%;', $disabled]);

              !!}

            </div>

          </div>

          {!!$message!!}

        </div>

        <div class="clearfix"></div>

        <div class="col-md-4">

          <div class="form-group">

            {!! Form::label('document', __('purchase.attach_document') . ':') !!}

            {!! Form::file('document'); !!}

          </div>

        </div>



        <div class="clearfix"></div>



        @include('transaction_payment.advance_payment_type_details')

        <div class="col-md-12">

          <div class="form-group">

            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}

            {!! Form::textarea("note", null, ['class' => 'form-control', 'rows' => 3]); !!}

          </div>

        </div>

      </div>

    </div>



    <div class="modal-footer">

      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>

      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>

    </div>



    {!! Form::close() !!}



  </div><!-- /.modal-content -->

</div><!-- /.modal-dialog -->



<script>

  $('#add_security_deposit_form').validate();

    $('.payment_types_dropdown').trigger('change');



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

    $(document).on('click', '#refund_btn', function(){

      $('#add_security_deposit_form').validate();

      if($('#add_security_deposit_form').valid()){

        let refund_transaction_id = $('#refund_transaction_id').val();

        let contact_id = {{$contact_id}};

        

        $.ajax({

          method: 'post',

          url: "/payments/refund-security-deposit/"+contact_id,

          data: { 

            refund_transaction_id : refund_transaction_id, 

            amount: $('#amount').val(), 

            payment_ref_no: 'R' +$('#payment_ref_no').val(),

            account_id: $('#account_id').val(), 

            current_liability_account: $('#current_liability_account').val(), 

            paid_on: $('#paid_on').val(), 

            method: $('#method').val(), 

            

          },

          success: function(result) {

            if(result.success === 1){

              toastr.success(result.msg)

              $('.pay_contact_due_modal').modal('hide')

            }else{

              toastr.error(result.msg)

            }

          },

        });



      }

      })
      
      
      $(document).on('change', '#amount', function(){
            @if($contact_details->type == 'supplier')
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
          
          @if($contact_details->type == 'supplier')
        	   // alert("checking");
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