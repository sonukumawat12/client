<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Subscription\Http\Controllers\SubscriptionSettingController@update', $subscription->id), 'method' =>
    'put', 'id' => 'edit_subscription_form', 'enctype' => 'multipart/form-data' ]) !!}
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'subscription::lang.subscription_settings' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('transaction_date', __( 'subscription::lang.date' )) !!} 
              {!! Form::date('transaction_date', date('Y-m-d',strtotime($subscription->transaction_date)), ['class' => 'form-control transaction_date',
              'required', 'readonly', 'placeholder' => __( 'subscription::lang.date' )]); !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('product', __( 'subscription::lang.product' )) !!} 
              {!! Form::text('product',$subscription->product, ['class' => 'form-control', 'required', 'placeholder' => __(
              'subscription::lang.product' ), 'required']); !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('base_amount', __( 'subscription::lang.base_amount' )) !!} 
              {!! Form::text('base_amount',$subscription->base_amount, ['class' => 'form-control', 'required', 'placeholder' => __(
              'subscription::lang.base_amount' ), 'required']); !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('subscription_cycle', __( 'subscription::lang.subscription_cycle' )) !!} 
              {!! Form::select('subscription_cycle', $subscription_cycles, $subscription->subscription_cycle, ['class' =>
              'form-control select2', 'required', 'placeholder' => __(
              'subscription::lang.please_select' )]); !!}
            </div>
          </div>
          
          
      </div>
    </div>
    
    <div class="modal-footer">
        
      <button type="submit" class="btn btn-primary" id="save_leads_btn">
        @lang( 'messages.update' )
      </button>
       
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    
    {!! Form::close() !!}
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->

<script>
    $(".select2").select2();
</script>

