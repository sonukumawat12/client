<div class="modal-dialog modal-lg" role="document" >
  <div class="modal-content">
    <style>
      .select2 {    
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Subscription\Http\Controllers\SubscriptionListController@save_payment',$list->id), 'method' =>
    'post', 'enctype' => 'multipart/form-data' ]) !!}
    <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'subscription::lang.subscription_list' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('transaction_date', __( 'subscription::lang.transaction_date' )) !!} 
              {!! Form::date('transaction_date', date('Y-m-d'), ['class' => 'form-control transaction_date',
              'required', 'placeholder' => __( 'subscription::lang.date' )]); !!}
            </div>
          </div>
          
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('subscription_amount', __( 'subscription::lang.subscription_amount' )) !!} 
              {!! Form::text('subscription_amount',@num_format($list->subscription_amount), ['class' => 'form-control subscription_amount', 'required','readonly', 'placeholder' => __(
              'subscription::lang.subscription_amount' ), 'required']); !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('expiry_date', __( 'subscription::lang.expiry_date' )) !!} 
              {!! Form::date('expiry_date',date('Y-m-d',strtotime($list->expiry_date)), ['class' => 'form-control expiry_date', 'required','readonly', 'placeholder' => __(
              'subscription::lang.expiry_date' ), 'required']); !!}
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('new_expiry_date', __( 'subscription::lang.new_expiry_date' )) !!} 
              {!! Form::date('new_expiry_date',date('Y-m-d',strtotime($list->new_expiry_date)), ['class' => 'form-control new_expiry_date', 'required','readonly', 'placeholder' => __(
              'subscription::lang.new_expiry_date' ), 'required']); !!}
            </div>
          </div>
          
           
            
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('created_by', __( 'subscription::lang.created_by' )) !!} 
              {!! Form::text('created_by',auth()->user()->username, ['class' => 'form-control', 'required','readonly', 'placeholder' => __(
              'subscription::lang.created_by' ), 'required']); !!}
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('note', __( 'subscription::lang.note' )) !!} 
              {!! Form::textarea('note',  null, ['class' => 'form-control','rows'=> '3', 'placeholder' => __( 'subscription::lang.note' )]); !!}
            </div>
          </div>
      
    </div>
    
    <div class="modal-footer">
        
      <button type="submit" class="btn btn-primary" id="save_leads_btn">
        @lang( 'messages.save' )
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