<div class="modal-dialog modal-lg" role="document" >
  <div class="modal-content">
    <style>
      .select2 {    
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Subscription\Http\Controllers\SubscriptionListController@update',$list->id), 'method' =>
    'put', 'enctype' => 'multipart/form-data' ]) !!}
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
    
    <input type="hidden" class="cycle_details" value="{{json_encode($cycle_details)}}">
    <input type="hidden" id="selected_settings_id" value="{{$list->settings_id}}">

    <div class="modal-body">
      <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('transaction_date', __( 'subscription::lang.date' )) !!} 
              {!! Form::date('transaction_date', date('Y-m-d',strtotime($list->transaction_date)), ['class' => 'form-control transaction_date',
              'required', 'placeholder' => __( 'subscription::lang.date' )]); !!}
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('contact_id', __( 'subscription::lang.customer' )) !!} 
              {!! Form::select('contact_id',$customers,$list->contact_id, ['class' => 'form-control select2', 'required', 'placeholder' => __(
              'subscription::lang.select_one' ), 'required']); !!}
            </div>
          </div>
          
          
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('subscription_cycle', __( 'subscription::lang.subscription_cycle' )) !!} 
              {!! Form::select('subscription_cycle',$cycles,$subscription_cycle, ['class' => 'form-control select2 subscription_cycle', 'required', 'placeholder' => __(
              'subscription::lang.select_one' ), 'required']); !!}
            </div>
          </div>
          
          <div class="clearfix"></div>
          
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('product_id', __( 'subscription::lang.product' )) !!} 
              {!! Form::select('product_id[]',[],json_decode($list->settings_id,true), ['class' => 'form-control select2 product_id', 'required','multiple', 'required']); !!}
            </div>
          </div>
          
          <div class="clearfix"></div>
          
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
              {!! Form::label('send_sms', __( 'subscription::lang.send_sms' )) !!} 
              {!! Form::select('send_sms',[1 => __('lang_v1.yes'),0 => __('lang_v1.no')],$list->send_sms, ['class' => 'form-control select2', 'required', 'placeholder' => __(
              'subscription::lang.select_one' ), 'required']); !!}
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
              {!! Form::textarea('note',  $list->note, ['class' => 'form-control','rows'=> '3', 'placeholder' => __( 'subscription::lang.note' )]); !!}
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
    $(".subscription_cycle").trigger('change');
</script>