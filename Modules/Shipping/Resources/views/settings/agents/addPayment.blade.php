
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\AgentController@postPayment', $agent->id), 'method' =>
    'post']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.agent' )</h4> 
    </div>

    <div class="modal-body">
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
              {!! Form::text("amount", 0, ['class' => 'form-control input_number',
              'data-rule-min-value' => 0, 'data-msg-min-value' => __('lang_v1.negative_value_not_allowed'), 'required',
              'placeholder' => 'Amount', 'readonly']); !!}
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
              {!! Form::text('paid_on', @format_datetime(\Carbon::now()->toDateTimeString()), ['class' => 'form-control',
              'readonly', 'required']); !!}
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
              {!! Form::select("method", $payment_types, null, ['class' => 'form-control select2
              payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("pending_invoices" , __('shipping::lang.pending_invoices') . ':*') !!}
            <select class="select2 form-control" id="pending_invoices" name="pending_invoices[]" multiple required>
                @foreach($pending_invoices as $invoice)
                    <option value="{{$invoice->id}}" data-string="{{$invoice->amount}}">{{$invoice->tracking_no}} ({{@num_format($invoice->amount)}})</option>
                @endforeach
            </select>
          </div>
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

              {!! Form::select("account_id", $accounts, null, 
                                ['class' => 'form-control select2 account_id', 'id' => "account_id", 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        @include('shipping::settings.agents.partials.payment_type_details')
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
    $(".select2").select2();
     $(document).ready(function () {
         $(".payment_types_dropdown").trigger('change');
     });
</script>




