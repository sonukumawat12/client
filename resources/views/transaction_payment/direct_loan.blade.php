<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('TransactionPaymentController@postDirectLoan', $contact_id), 'method' => 'post', 'id' => 'pay_contact_due_form', 'files' => true ]) !!}
  
      {!! Form::hidden("contact_id", $contact_details->contact_id); !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'lang_v1.direct_loan' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="well">
              @if($contact_details->type == 'customer')
              <strong>@lang('lang_v1.customer'): 
                @else
                <strong>@lang('lang_v1.supplier'): 
              @endif
              </strong>{{ $contact_details->name }}<br>
            </div>
          </div>
          <input type="hidden" name="type" value="advance_payment">
        </div>
        <div class="row payment_row"> 
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("amount" , __('sale.amount') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::text("amount", null, ['class' => 'form-control input_number', 'data-rule-min-value' => 0, 'data-msg-min-value' => __('lang_v1.negative_value_not_allowed'), 'required', 'placeholder' => 'Amount']); !!}
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("user" , __('lang_v1.created_by') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::text("user", auth()->user()->username, ['class' => 'form-control','disabled']); !!}
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("method", $accounts, null, ['class' => 'form-control select2 ', 'disabled', 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
          
        <div class="col-md-6 account_id_div">
          <div class="form-group">
            {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::select("account_id", $accounts, null, ['class' => 'form-control select2 ', 'id' => "account_id", 'style' => 'width:100%;','disabled']); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </span>
                {!! Form::text('paid_on', date('m/d/Y'), ['class' => 'form-control', 'readonly', 'required']); !!}
              </div>
            </div>
          </div>
        
        
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
      $('#pay_contact_due_form').validate();
       $(document).on('click','#amount',function(){
       $("#amount").val("");
    });
  </script>