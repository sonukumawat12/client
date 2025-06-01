<div class="modal-dialog modal-sm" role="document" >
  <div class="modal-content">
    <style>
      .select2 {    
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\Subscription\Http\Controllers\SubscriptionListController@update_status',$list->id), 'method' =>
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
          
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('status', __( 'subscription::lang.customer' )) !!} 
              {!! Form::select('status',[1 => __('subscription::lang.active'),0 => __('subscription::lang.inactive')],$list->status, ['class' => 'form-control select2', 'required', 'placeholder' => __(
              'subscription::lang.select_one' ), 'required']); !!}
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