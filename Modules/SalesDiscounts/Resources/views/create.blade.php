<div class="modal-dialog modal-lg" role="document" >
  <div class="modal-content">
    <style>
      .select2 {
        width: 100% !important;
      }
    </style>
    {!! Form::open(['url' =>
    action('\Modules\SalesDiscounts\Http\Controllers\SalesDiscountsController@store'), 'method' =>
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
      <h4 class="modal-title">@lang( 'salesdiscounts::lang.salesdiscounts' )</h4>
    </div>
    
    <input type="hidden" class="cycle_details">

    <div class="modal-body">
      <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('transaction_date', __( 'salesdiscounts::lang.transaction_date' )) !!} 
              {!! Form::date('transaction_date', date('Y-m-d'), ['class' => 'form-control transaction_date',
              'required', 'placeholder' => __( 'salesdiscounts::lang.transaction_date' )]); !!}
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('customer', __( 'salesdiscounts::lang.customer' )) !!} 
              {!! Form::select('customer',$customers,null, ['class' => 'form-control select2', 'required', 'placeholder' => __(
              'subscription::lang.select_one' ), 'required']); !!}
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('location', __( 'salesdiscounts::lang.location' )) !!} 
              {!! Form::select('location',$business_locations,null, ['class' => 'form-control select2 location', 'required', 'placeholder' => __(
              'salesdiscounts::lang.select_one' ), 'required']); !!}
            </div>
          </div>
          
        
          
          <div class="clearfix"></div>
          
            <div class="col-md-3">
            <div class="form-group">
               {!! Form::label('subscription_amounts', __( 'salesdiscounts::lang.invoice_no' )) !!} 
              {!! Form::text('subscription_amounts',null, ['class' => 'form-control subscription_amounts', 'required', 'placeholder' => __(
              'salesdiscounts::lang.invoice_no' ), 'required']); !!}
            </div>
           </div>
         <div class="col-md-3">
            <div class="form-group">
             {!! Form::label('type', __('salesdiscounts::lang.discount_type') . ':') !!}
                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'discount_type', 'placeholder' => __('salesdiscounts::lang.all')]); !!}
            </div>
          </div>
           <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('subscription_amount', __( 'salesdiscounts::lang.discount_amount' )) !!} 
              {!! Form::text('subscription_amount',null, ['class' => 'form-control subscription_amount', 'required', 'placeholder' => __(
              'salesdiscounts::lang.discount_amount' ), 'required']); !!}
            </div>
          </div>
         
            
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('created_by', __( 'salesdiscounts::lang.created_by' )) !!} 
              {!! Form::text('created_by',auth()->user()->username, ['class' => 'form-control', 'required','readonly', 'placeholder' => __(
              'salesdiscounts::lang.created_by' ), 'required']); !!}
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